<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Validator;
use Storage;

class AuthController extends Controller
{
    public function username()
    {
        $login = request()->input('username');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';        
        request()->merge([$field => $login]);
        return $field;
        //return 'username';
    }
    public function register(Request $request)
    {
        $request->validate(
            [
                'name' => 'required|string',
                'email'=>'required|string|unique:users',
                'password'=>'required|string',
                'c_password' => 'required|same:password'
            ],
            [
                'name.required' => 'Nama tidak boleh kosong',
                'email.required' => 'Email tidak boleh kosong',
                'email.unique' => 'Akun telah terdaftar',
                'password.required' => 'Password tidak boleh kosong'
            ]
        );

        $user = new User([
            'name'  => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        if($user->save()){
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->plainTextToken;

            return response()->json([
                'message' => 'Successfully created user!',
                'accessToken'=> $token,
                'user' => $user,
            ],201);
        }
        else{
            return response()->json(['error'=>'Provide proper details']);
        }
    }
    public function login(Request $request)
    {
        $login = request()->input('email');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $namaField = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'Email' : 'NPSN';
        request()->merge([$field => $login]);
        $request->validate(
            [
                $field => 'required|string|exists:users,'.$field ,
                'password' => 'required|string',
                'remember_me' => 'boolean'
            ],
            [
                $field.'.required' => $namaField.' tidak boleh kosong',
                $field.'.exists' => $namaField.' tidak terdaftar',
                'password.required' => 'Password tidak boleh kosong'
            ]
        );
        $credentials = request([$field,'password']);
        //return response()->json($credentials);
        if(!Auth::attempt($credentials)){
            return response()->json([
                'user' => NULL,
                'message' => [
                    'password' => 'Password salah!',
                ]
            ],401);
        }

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->plainTextToken;
        $user = $this->loggedUser($user);
        return response()->json([
            'accessToken' =>$token,
            'user' => $user,
            'token_type' => 'Bearer',
        ]);
    }
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
        'message' => 'Successfully logged out'
        ]);

    }
    public function login_by_token(){
        $key = '76786b0f4f77d768092ce0ab95aafa67eb7950d9c7406797093a11d58a7d1260';
        $response = Http::get('http://103.40.55.249/Do/GetLoginInfo', [
            'access_token' => request()->access_token,
            'key' => $key,
        ]);
        $json = $response->object();
        //$json = json_decode(Storage::disk('public')->get('simazah.test.json'));
        $user = NULL;
        $token = NULL;
        if($json->user_profile && $json->user_profile->peran_id == 10){
            $user = User::where('email', $json->user_profile->username)->first();
            if($user){
                Auth::loginUsingId($user->user_id);
                $tokenResult = $user->createToken('Personal Access Token');
                $token = $tokenResult->plainTextToken;
                $user = $this->loggedUser($user);
            }
        }
        if($user && $user->sekolah){
            return response()->json([
                'accessToken' =>$token,
                'user' => $user,
                'token_type' => 'Bearer',
            ]);
        } else {
            return response()->json([
                'accessToken' =>$token,
                'user' => NULL,
                'token_type' => 'Bearer',
            ]);
        }
        //Auth::loginUsingId(1);
    }
    private function loggedUser($user){
        if($user->hasRole('administrator')){
            $user->ability = [
                [
                    'action' => 'read',
                    'subject' => 'Web'
                ],
                [
                    'action' => 'read',
                    'subject' => 'Administrator'
                ],
                [
                    'action' => 'read',
                    'subject' => 'Akses'
                ]
            ];
        } elseif($user->hasRole('guru')){ //dpw
            $user->ability = [
                [
                    'action' => 'read',
                    'subject' => 'Web'
                ],
                [
                    'action' => 'read',
                    'subject' => 'Guru'
                ],
                [
                    'action' => 'read',
                    'subject' => 'Akses'
                ]
            ];
        } elseif($user->hasRole('siswa')){ //alumni_aktif
            $user->ability = [
                [
                    'action' => 'read',
                    'subject' => 'Web'
                ],
                [
                    'action' => 'read',
                    'subject' => 'Siswa'
                ],
                [
                    'action' => 'read',
                    'subject' => 'Akses'
                ],
            ];
        } else { //alumni_nonaktif
            $user->ability = [
                [
                    'action' => 'read',
                    'subject' => 'Web'
                ]
            ];
        }
        $user->role = $user->roles->implode('display_name', ', ');
        //$user->sekolah = $user->sekolah;
        //$user->wilayah = $user->wilayah;
        return $user;
    }
}
