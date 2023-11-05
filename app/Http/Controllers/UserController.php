<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Role;

class UserController extends Controller
{
    public function loggedUser(){
        return auth()->user();
    }
    public function index(){
        $data = User::where($this->kondisi())->orderBy(request()->sortby, request()->sortbydesc)
        ->when(request()->q, function($query) {
            $query->where('name', 'ilike', '%'.request()->q.'%');
            $query->orWhere('email', 'ilike', '%'.request()->q.'%');
        })
        ->paginate(request()->per_page);
        return response()->json(['status' => 'success', 'data' => $data]);
    }
    public function reset_password(){
        $user = User::find(request()->user_id);
        if($user){
            $user->password = bcrypt(12345678);
            if($user->save()){
                $data = [
                    'icon' => 'success',
                    'title' => 'Berhasil!',
                    'text' => 'Password Pengguna berhasil direset',
                ];
            } else {
                $data = [
                    'icon' => 'error',
                    'title' => 'Gagal!',
                    'text' => 'Password Pengguna gagal direset. Silahkan coba beberapa saat lagi!',
                ];
            }
        } else {
            $data = [
                'icon' => 'error',
                'title' => 'Gagal!',
                'text' => 'Pengguna tidak ditemukan. Silahkan coba beberapa saat lagi!',
            ];
        }
        return response()->json($data);
    }
    public function profile(){
        $user = User::find($this->loggedUser()->user_id);
        return response()->json($user);
    }
    public function update_profile(){
        $user = User::find($this->loggedUser()->user_id);
        if(request()->has('name')){
            $message = [
                'name.required' => 'Nama Lengkap tidak boleh kosong',
                'email.required' => 'Email tidak boleh kosong',
                'email.email' => 'Email tidak valid',
                'email.unique' => 'Email telah terdaftar',
            ];
            $rules = [
                'name' => ['required'],
                'email' => [
                    'required',
                    'email',
                    Rule::unique('users')->ignore($this->loggedUser()->user_id, 'user_id'),
                ],
            ];
            $validator = Validator::make(request()->all(), $rules, $message)->validated();
            $user->name = request()->name;
            $user->email = request()->email;
            if($user->save()){
                $data = [
                    'icon' => 'success',
                    'title' => 'Berhasil!',
                    'text' => 'Profil Pengguna berhasil diperbaharui',
                ];
            } else {
                $data = [
                    'icon' => 'danger',
                    'title' => 'Gagal!',
                    'text' => 'Profil Pengguna gagal diperbaharui. Silahkan coba beberapa saat lagi!',
                ];
            }
        } else {
            $message = [
                //'current_password.required' => 'Kata sandi saat ini tidak boleh kosong',
                //'current_password.current_password' => 'Kata sandi salah',
                'password.required' => 'Kata sandi baru tidak boleh kosong',
                'password.confirmed' => 'Konfirmasi kata sandi tidak sesuai dengan Kata sandi baru',
                'password_confirmation.required' => 'Konfirmasi kata sandi tidak boleh kosong',
            ];
            $rules = [
                //'current_password' => ['required', 'current_password'],
                'password' => [
                    'required',
                    'confirmed',
                ],
                'password_confirmation' => ['required'],
            ];
            $validator = Validator::make(request()->all(), $rules, $message)->validated();
            $user->password = Hash::make(request()->password);
            if($user->save()){
                $data = [
                    'icon' => 'success',
                    'title' => 'Berhasil!',
                    'text' => 'Password Pengguna berhasil diperbaharui',
                ];
            } else {
                $data = [
                    'icon' => 'danger',
                    'title' => 'Gagal!',
                    'text' => 'Password Pengguna gagal diperbaharui. Silahkan coba beberapa saat lagi!',
                ];
            }
        }
        return response()->json($data);
    }
    public function hapus(){
        $user = User::find(request()->user_id);
        if($user){
            if($user->delete()){
                $data = [
                    'icon' => 'success',
                    'title' => 'Berhasil!',
                    'text' => 'Pengguna berhasil dihapus',
                ];
            } else {
                $data = [
                    'icon' => 'error',
                    'title' => 'Gagal!',
                    'text' => 'Pengguna gagal dihapus. Silahkan coba beberapa saat lagi!',
                ];
            }
        } else {
            $data = [
                'icon' => 'error',
                'title' => 'Gagal!',
                'text' => 'Pengguna tidak ditemukan. Silahkan coba beberapa saat lagi!',
            ];
        }
        return response()->json($data);
    }
}
