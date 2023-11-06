<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Models\Peserta_didik;
use App\Models\Ujian;

class DownloadController extends Controller
{
    public function nilai_ujian(){
        $ujian = Ujian::with(['pembelajaran', 'soal' => function($query){
            $query->orderBy('nomor');
        }])->find(request()->route('ujian_id'));
        $siswa = Peserta_didik::whereHas('anggota_rombel', function($query) use ($ujian){
            $query->where('rombongan_belajar_id', $ujian->pembelajaran->rombongan_belajar_id);
        })->with(['user'])->withCount([
            'jawaban_siswa as benar' => function($query){
                $query->whereHas('jawaban', function($query){
                    $query->where('benar', 1);
                });
                $query->whereHas('soal', function($query){
                    $query->where('ujian_id', request()->route('ujian_id'));
                });
            },
            'jawaban_siswa as salah' => function($query){
                $query->whereHas('jawaban', function($query){
                    $query->where('benar', 0);
                });
                $query->whereHas('soal', function($query){
                    $query->where('ujian_id', request()->route('ujian_id'));
                });
            },
        ])->orderBy('nama')->get();
        $result = [];
        $data = [];
        $i=1;
        foreach($siswa as $pd){
            $data['NO'] = $i;
            $data['NAMA'] = $pd->nama;
            $data['NISN'] = $pd->nisn;
            foreach($ujian->soal as $soal){
                $jawaban_siswa = $soal->jawaban_siswa()->where('user_id', $pd->user->user_id)->first();
                $data[$soal->nomor] = ($jawaban_siswa) ? $jawaban_siswa->opsi : '';
            }
            $nilai = ($pd->benar*10)/4;
            $data['BENAR'] = $pd->benar;
            $data['SALAH'] = $pd->salah;
            $data['NILAI'] = number_format($nilai, 0, '.', '.');
            $result[] = $data;
            $i++;
        }
        $satuan = strtoupper('hasil ujian '.$ujian->nama.' mapel '.$ujian->pembelajaran->nama_mata_pelajaran.' kelas '.$ujian->pembelajaran->rombongan_belajar->nama);
        return (new FastExcel($result))->download($satuan.'.xlsx');
    }
}
