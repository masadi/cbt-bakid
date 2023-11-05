<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rombongan_belajar;
use App\Models\Pembelajaran;
use App\Models\Mata_pelajaran;
use App\Models\Guru;

class RombelController extends Controller
{
    public function index(){
        $data = Rombongan_belajar::with('walas')->withCount('anggota_rombel')->orderBy(request()->sortby, request()->sortbydesc)->orderBy('tingkat')
        ->when(request()->q, function($query) {
            $query->where('nama', 'ilike', '%'.request()->q.'%');
        })
        ->paginate(request()->per_page);
        return response()->json(['status' => 'success', 'data' => $data]);
    }
    public function pembelajaran(){
        $rombel = Rombongan_belajar::find(request()->rombongan_belajar_id);
        $data = Mata_pelajaran::with(['pembelajaran' => function($query){
            $query->with(['guru']);
            $query->where('rombongan_belajar_id', request()->rombongan_belajar_id);
        }])->orderBy('mata_pelajaran_id')->get();
        //Pembelajaran::with(['guru'])->where('rombongan_belajar_id', request()->rombongan_belajar_id)->orderBy('mata_pelajaran_id')->get();
        return response()->json([
            'data' => $data,
            'guru' => Guru::orderBy('nama')->get(),
            'judul' => 'Pembelajaran '.$rombel->nama,
        ]);
    }
    public function wali_kelas(){
        $rombel = Rombongan_belajar::find(request()->rombongan_belajar_id);
        return response()->json([
            'rombel' => $rombel,
            'guru' => Guru::orderBy('nama')->get(),
            'judul' => 'Wali Kelas '.$rombel->nama,
        ]);
    }
    public function simpan_wali_kelas(){
        $rombel = Rombongan_belajar::find(request()->rombongan_belajar_id);
        $rombel->guru_id = request()->guru_id;
        if($rombel->save()){
            $data = [
                'icon' => 'success',
                'title' => 'Berhasil',
                'text' => 'Wali Kelas berhasil disimpan',
            ];
        } else {
            $data = [
                'icon' => 'error',
                'title' => 'Gagal',
                'text' => 'Wali Kelas gagal disimpan. Silahkan coba beberapa saat lagi!',
            ];
        }
        return response()->json($data);
    }
    public function simpan_pembelajaran(){
        $insert = NULL;
        foreach(request()->guru_id as $key => $guru_id){
            if($guru_id){
                $mapel = Mata_pelajaran::find(request()->mata_pelajaran_id[$key]);
                $insert = Pembelajaran::updateOrCreate(
                    [
                        'rombongan_belajar_id' => request()->rombongan_belajar_id,
                        'mata_pelajaran_id' => request()->mata_pelajaran_id[$key],
                    ],
                    [
                        'nama_mata_pelajaran' => $mapel->nama,
                        'guru_id' => $guru_id,
                    ]
                );
            }
        }
        if($insert){
            $data = [
                'icon' => 'success',
                'title' => 'Berhasil',
                'text' => 'Pembelajaran berhasil disimpan',
            ];
        } else {
            $data = [
                'icon' => 'error',
                'title' => 'Gagal',
                'text' => 'Pembelajaran gagal disimpan. Silahkan coba beberapa saat lagi!',
            ];
        }
        return response()->json($data);
    }
    public function hapus_pembelajaran(){
        $pembelajaran = Pembelajaran::find(request()->pembelajaran_id);
        if($pembelajaran){
            if($pembelajaran->delete()){
                $data = [
                    'icon' => 'success',
                    'title' => 'Berhasil',
                    'text' => 'Pembelajaran berhasil dihapus',
                    'data_mapel' => $this->get_mapel(),
                ];
            } else {
                $data = [
                    'icon' => 'error',
                    'title' => 'Gagal',
                    'text' => 'Pembelajaran gagal dihapus. Silahkan coba beberapa saat lagi!',
                    'data_mapel' => $this->get_mapel(),
                ];
            }
        } else {
            $data = [
                'icon' => 'error',
                'title' => 'Gagal',
                'text' => 'Pembelajaran Tidak ditemukan. Silahkan coba beberapa saat lagi!',
                'data_mapel' => $this->get_mapel(),
            ];
        }
        return response()->json($data);
    }
    private function get_mapel(){
        $data_mapel = Mata_pelajaran::with(['pembelajaran' => function($query){
            $query->with(['guru']);
            $query->where('rombongan_belajar_id', request()->rombongan_belajar_id);
        }])->orderBy('mata_pelajaran_id')->get();
        return $data_mapel;
    }
}
