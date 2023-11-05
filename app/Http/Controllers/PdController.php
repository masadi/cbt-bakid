<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\Peserta_didik;
use App\Models\User;

class PdController extends Controller
{
    public function index(){
        $data = Peserta_didik::with('kelas')->orderBy(request()->sortby, request()->sortbydesc)
        ->when(request()->q, function($query) {
            $query->where('nama', 'ilike', '%'.request()->q.'%');
        })
        ->paginate(request()->per_page);
        return response()->json(['status' => 'success', 'data' => $data]);
    }
    public function hapus(){
        $find = Peserta_didik::find(request()->peserta_didik_id);
        if($find){
            if($find->delete()){
                $data = [
                    'icon' => 'success',
                    'title' => 'Berhasil',
                    'text' => 'Peserta Didik berhasil dihapus',
                ];
            } else {
                $data = [
                    'icon' => 'error',
                    'title' => 'Gagal',
                    'text' => 'Peserta Didik gagal dihapus. Silahkan coba beberapa saat lagi!',
                ];
            }
        } else {
            $data = [
                'icon' => 'error',
                'title' => 'Gagal',
                'text' => 'Peserta Didik tidak ditemukan. Silahkan coba beberapa saat lagi!',
            ];
        }
        return response()->json($data);
    }
    public function update(){
        $validator = Validator::make(request()->all(), 
            [
                'nisn' => [
                    'required',
                    Rule::unique('peserta_didik', 'nisn')->ignore(request()->peserta_didik_id, 'peserta_didik_id'),
                ],
                'peserta_didik_id' => ['required'],
                'tanggal_lahir' => 'required|date',
                'tempat_lahir' => ['required'],
                'jenis_kelamin' => ['required'],
            ],
            [
                'peserta_didik_id.required' => 'ID Peserta Didik tidak boleh kosong',
                'nama.required' => 'Nama Lengkap tidak boleh kosong',
                'nisn.required' => 'NISN tidak boleh kosong',
                'nisn.unique' => 'NISN telah terdaftar',
                'tanggal_lahir.required' => 'Tanggal Lahir tidak boleh kosong',
                'tanggal_lahir.date' => 'Tanggal Lahir tidak valid',
                'jenis_kelamin.required' => 'Jenis Kelamin tidak boleh kosong',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => FALSE,
                'errors' => $validator->errors(),
            ]);
        }
        $find = Peserta_didik::find(request()->peserta_didik_id);
        $find->nama = request()->nama;
        $find->nisn = request()->nisn;
        $find->tempat_lahir = request()->tempat_lahir;
        $find->tanggal_lahir = request()->tanggal_lahir;
        $find->jenis_kelamin = request()->jenis_kelamin;
        if(request()->photo){
            $photo = request()->photo->store('public/images');
            $find->photo = basename($photo);
        }
        if($find->save()){
            $user = User::where('peserta_didik_id', request()->peserta_didik_id)->first();
            if($user){
                $user->username = request()->nisn;
                $user->name = request()->nama;
                $user->email = request()->nisn.'@email.com';
                $user->save();
            } else {
                $user = User::create([
                    'peserta_didik_id' => request()->peserta_didik_id,
                    'username' => request()->nisn,
                    'name' => request()->nama,
                    'email' => request()->nisn.'@email.com',
                    'password' => bcrypt('12345678'),
                ]);
                $user->attachRole('siswa');
            }
            $data = [
                'success' => TRUE,
                'errors' => NULL,
                'icon' => 'success',
                'text' => 'Data Peserta Didik berhasil diperbaharui!',
                'title' => 'Berhasil',
            ];
        } else {
            $data = [
                'success' => TRUE,
                'errors' => NULL,
                'icon' => 'error',
                'text' => 'Data Peserta Didik gagal diperbaharui. Silahkan coba beberapa saat lagi!',
                'title' => 'Gagal',
            ];
        }
        return response()->json($data);
    }
}
