<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\Guru;
use App\Models\User;

class GuruController extends Controller
{
    public function index(){
        $data = Guru::orderBy(request()->sortby, request()->sortbydesc)
        ->when(request()->q, function($query) {
            $query->where('nama', 'ilike', '%'.request()->q.'%');
        })
        ->paginate(request()->per_page);
        return response()->json(['status' => 'success', 'data' => $data]);
    }
    public function hapus(){
        $find = Guru::find(request()->guru_id);
        if($find){
            if($find->delete()){
                $data = [
                    'icon' => 'success',
                    'title' => 'Berhasil',
                    'text' => 'Guru berhasil dihapus',
                ];
            } else {
                $data = [
                    'icon' => 'error',
                    'title' => 'Gagal',
                    'text' => 'Guru gagal dihapus. Silahkan coba beberapa saat lagi!',
                ];
            }
        } else {
            $data = [
                'icon' => 'error',
                'title' => 'Gagal',
                'text' => 'Guru tidak ditemukan. Silahkan coba beberapa saat lagi!',
            ];
        }
        return response()->json($data);
    }
    public function update(){
        $validator = Validator::make(request()->all(), 
            [
                'nuptk' => [
                    Rule::unique('guru', 'nuptk')->ignore(request()->guru_id, 'guru_id'),
                ],
                'nip' => [
                    Rule::unique('guru', 'nip')->ignore(request()->guru_id, 'guru_id'),
                ],
                'guru_id' => ['required'],
                'nama' => ['required'],
                'jenis_kelamin' => ['required'],
            ],
            [
                'guru_id.required' => 'ID Guru tidak boleh kosong',
                'nama.required' => 'Nama Lengkap tidak boleh kosong',
                'nuptk.unique' => 'NUPTK telah terdaftar',
                'nip.unique' => 'NIP telah terdaftar',
                'jenis_kelamin.required' => 'Jenis Kelamin tidak boleh kosong',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => FALSE,
                'errors' => $validator->errors(),
            ]);
        }
        $find = Guru::find(request()->guru_id);
        $find->nama = request()->nama;
        $find->nuptk = request()->nuptk;
        $find->nip = request()->nip;
        $find->email = request()->email;
        $find->jenis_kelamin = request()->jenis_kelamin;
        if(request()->photo){
            $photo = request()->photo->store('public/images');
            $find->photo = basename($photo);
        }
        if($find->save()){
            $user = User::where('guru_id', request()->guru_id)->first();
            if($user){
                $user->username = (request()->nuptk) ? request()->nuptk : request()->email;
                $user->name = request()->nama;
                $user->email = request()->email;
                $user->save();
            } else {
                $user = User::create([
                    'guru_id' => request()->guru_id,
                    'username' => (request()->nuptk) ? request()->nuptk : request()->email,
                    'name' => request()->nama,
                    'email' => request()->email,
                    'password' => bcrypt('12345678'),
                ]);
                $user->attachRole('guru');
            }
            $data = [
                'success' => TRUE,
                'errors' => NULL,
                'icon' => 'success',
                'text' => 'Data Guru berhasil diperbaharui!',
                'title' => 'Berhasil',
            ];
        } else {
            $data = [
                'success' => TRUE,
                'errors' => NULL,
                'icon' => 'error',
                'text' => 'Data Guru gagal diperbaharui. Silahkan coba beberapa saat lagi!',
                'title' => 'Gagal',
            ];
        }
        return response()->json($data);
    }
}
