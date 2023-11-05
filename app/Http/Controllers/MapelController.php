<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\Mata_pelajaran;

class MapelController extends Controller
{
    public function index(){
        $data = Mata_pelajaran::orderBy(request()->sortby, request()->sortbydesc)
        ->when(request()->q, function($query) {
            $query->where('nama', 'ilike', '%'.request()->q.'%');
        })
        ->paginate(request()->per_page);
        return response()->json(['status' => 'success', 'data' => $data]);
    }
    public function hapus(){
        $find = Mata_pelajaran::find(request()->mata_pelajaran_id);
        if($find){
            if($find->delete()){
                $data = [
                    'icon' => 'success',
                    'title' => 'Berhasil',
                    'text' => 'Mata Pelajaran berhasil dihapus',
                ];
            } else {
                $data = [
                    'icon' => 'error',
                    'title' => 'Gagal',
                    'text' => 'Mata Pelajaran gagal dihapus. Silahkan coba beberapa saat lagi!',
                ];
            }
        } else {
            $data = [
                'icon' => 'error',
                'title' => 'Gagal',
                'text' => 'Mata Pelajaran tidak ditemukan. Silahkan coba beberapa saat lagi!',
            ];
        }
        return response()->json($data);
    }
    public function update(){
        $validator = Validator::make(request()->all(), 
            [
                'nama' => [
                    'required',
                    Rule::unique('mata_pelajaran', 'nama')->ignore(request()->mata_pelajaran_id, 'mata_pelajaran_id'),
                ],
                'mata_pelajaran_id' => ['required'],
            ],
            [
                'mata_pelajaran_id.required' => 'ID Mata Pelajaran tidak boleh kosong',
                'nama.required' => 'Nama Mata Pelajaran tidak boleh kosong',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => FALSE,
                'errors' => $validator->errors(),
            ]);
        }
        $find = Mata_pelajaran::find(request()->mata_pelajaran_id);
        $find->nama = request()->nama;
        if($find->save()){
            $data = [
                'success' => TRUE,
                'errors' => NULL,
                'icon' => 'success',
                'text' => 'Data Mata Pelajaran berhasil diperbaharui!',
                'title' => 'Berhasil',
            ];
        } else {
            $data = [
                'success' => TRUE,
                'errors' => NULL,
                'icon' => 'error',
                'text' => 'Data Mata Pelajaran gagal diperbaharui. Silahkan coba beberapa saat lagi!',
                'title' => 'Gagal',
            ];
        }
        return response()->json($data);
    }
}
