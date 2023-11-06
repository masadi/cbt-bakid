<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Models\Ujian;
use App\Models\Soal;
use App\Models\Jawaban;
use App\Models\Pembelajaran;
use ZipArchive;
use File;

class SoalController extends Controller
{
    private function loggedUser(){
        return auth()->user();
    }
    public function index(){
        $data = Ujian::where(function($query){
            $query->whereHas('pembelajaran', function($query){
                $query->where('guru_id', $this->loggedUser()->guru_id);
            });
        })->with(['pembelajaran.rombongan_belajar'])->orderBy(request()->sortby, request()->sortbydesc)
        ->when(request()->q, function($query) {
            $query->where('nama', 'ilike', '%'.request()->q.'%');
            $query->orWhereHas('pembelajaran', function($query){
                $query->where('nama_mata_pelajaran', 'ilike', '%'.request()->q.'%');
            });
        })
        ->when(request()->rombel_id, function($query) {
            $query->orWhereHas('pembelajaran', function($query){
                $query->where('rombongan_belajar_id', request()->rombel_id);
            });
        })
        ->when(request()->mapel_id, function($query) {
            $query->where('pembelajaran_id', request()->mapel_id);
        })
        ->paginate(request()->per_page);
        $pembelajaran = Pembelajaran::with(['rombongan_belajar'])->where(function($query){
            $query->where('guru_id', $this->loggedUser()->guru_id);
        })->orderBy('mata_pelajaran_id')->get();
        return response()->json(['status' => 'success', 'data' => $data, 'pembelajaran' => $pembelajaran]);
    }
    public function soal(){
        $ujian = Ujian::find(request()->ujian_id);
        $data = Soal::with(['jawaban' => function($query){
            $query->orderBy('urut');
        }])->where(function($query){
            $query->where('ujian_id', request()->ujian_id);
            $query->where('nomor', request()->nomor);
        })->first();
        return response()->json([
            'data' => $data,
            'ujian' => $ujian,
        ]);
    }
    public function simpan(){
        $soal = Soal::where('soal_id', request()->soal_id)->update(['deskripsi' => request()->soal]);
        foreach(request()->jawaban as $jawaban_id => $deskripsi){
            Jawaban::where('jawaban_id', $jawaban_id)->update(['deskripsi' => $deskripsi]);
        }
        $data = ['data' => request()->all()];
        return response()->json($data);
    }
    public function update(){
        $validator = Validator::make(request()->all(), 
            [
                'nama' => ['required'],
                'status' => ['required'],
            ],
            [
                'nama.required' => 'Nama Mata Ujian tidak boleh kosong',
                'status.required' => 'Status Ujian tidak boleh kosong',
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'data' => NULL,
                'success' => FALSE,
                'errors' => $validator->errors(),
            ]);
        }
        $find = Ujian::find(request()->ujian_id);
        $find->nama = request()->nama;
        $find->status = request()->status;
        if($find->save()){
            $data = [
                'success' => TRUE,
                'errors' => NULL,
                'icon' => 'success',
                'text' => 'Mata Ujian berhasil diperbaharui',
                'title' => 'Berhasil',
            ];
        } else {
            $data = [
                'success' => TRUE,
                'errors' => NULL,
                'icon' => 'error',
                'text' => 'Mata Ujian gagal diperbaharui. Silahkan coba beberapa saat lagi!',
                'title' => 'Gagal',
            ];
        }
        return response()->json($data);
    }
    public function hapus(){
        $find = Ujian::find(request()->ujian_id);
        if($find){
            if($find->delete()){
                $data = [
                    'success' => TRUE,
                    'errors' => NULL,
                    'icon' => 'success',
                    'text' => 'Mata Ujian berhasil dihapus',
                    'title' => 'Berhasil',
                ];
            } else {
                $data = [
                    'success' => TRUE,
                    'errors' => NULL,
                    'icon' => 'error',
                    'text' => 'Mata Ujian gagal dihapus. Silahkan coba beberapa saat lagi!',
                    'title' => 'Gagal',
                ];
            }
        } else {
            $data = [
                'success' => TRUE,
                'errors' => NULL,
                'icon' => 'error',
                'text' => 'Mata Ujian tidak ditemukan. Silahkan coba beberapa saat lagi!',
                'title' => 'Gagal',
            ];
        }
        return response()->json($data);
    }
    public function upload(){
        $validator = Validator::make(request()->all(), 
            [
                'nama' => ['required'],
                'pembelajaran_id' => ['required'],
                'file_zip' => ['required', 'file', 'mimes:zip'],
                'status' => ['required'],
            ],
            [
                'nama.required' => 'Nama Mata Ujian tidak boleh kosong',
                'pembelajaran_id.required' => 'Pembelajaran tidak boleh kosong',
                'file_zip.required' => 'Berkas ZIP tidak boleh kosong',
                'file_zip.file' => 'Berkas ZIP tidak boleh kosong',
                'file_zip.mimes' => 'Berkas ZIP harus berekstensi .ZIP',
                'status.required' => 'Status Ujian tidak boleh kosong',
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'data' => NULL,
                'success' => FALSE,
                'errors' => $validator->errors(),
            ]);
        }
        $insert = 0;
        $files = [];
        $images = [];
        $zip = new ZipArchive();
        $status = $zip->open(request()->file('file_zip')->getRealPath());
        if ($status !== true) {
         throw new \Exception($status);
        } else {
            $storageDestinationPath= storage_path('app/uploads/'.request()->pembelajaran_id.'/');
       
            if (!File::exists( $storageDestinationPath)) {
                File::makeDirectory($storageDestinationPath, 0755, true);
            }
            $zip->extractTo($storageDestinationPath);
            $zip->close();
            $files = File::files($storageDestinationPath);
            $file_excel = NULL;
            foreach($files as $file){
                if($file->getExtension() == 'xlsx'){
                    $file_excel = $file->getFileName();
                } else {
                    $random = Str::random(40);
                    $extension = File::extension($file->getFileName());
                    $file_image = $random.'.'.$extension;
                    Storage::move('uploads/'.request()->pembelajaran_id.'/'.$file->getFileName(), 'public/images/'.request()->pembelajaran_id.'/'.$file_image);
                    $images[$file_image] = $file->getFileName();
                }
                //$file->getFileName();
            }
            $ujian = Ujian::create(
                [
                    'pembelajaran_id' => request()->pembelajaran_id,
                    'status' => request()->status,
                    'nama' => request()->nama,
                ]
            );
            $this->import_soal($images, $ujian, $storageDestinationPath, $file_excel);
            $insert = 1;
        }
        if($insert){
            $data = [
                'data' => $images,
                'success' => TRUE,
                'errors' => NULL,
                'icon' => 'success',
                'text' => 'Mata Ujian berhasil disimpan',
                'title' => 'Berhasil',
            ];
        } else {
            $data = [
                'data' => request()->all(),
                'success' => TRUE,
                'errors' => NULL,
                'icon' => 'error',
                'text' => 'Mata Ujian gagal disimpan. Silahkan coba beberapa saat lagi!',
                'title' => 'Gagal',
            ];
        }
        return response()->json($data);
    }
    private function create_deskripsi($images, $item){
        $converted = Str::of($item)->between('#', '#');
        foreach($images as $gambar => $image){
            $nama_gambar = pathinfo($image, PATHINFO_FILENAME);
            if($item !== $converted && $nama_gambar == $converted){
                $item = str_replace('#'.$converted.'#', '<img src="/storage/images/'.request()->pembelajaran_id.'/'.$gambar.'" class="img-fluid" />', $item);
            }
        }
        return nl2br($item);
    }
    private function import_soal($images, $ujian, $folder, $file){
        $users = (new FastExcel)->import($folder.$file, function ($item) use ($ujian, $images){
            //dd($item);
            $soal = Soal::updateOrCreate(
                [
                    'ujian_id' => $ujian->ujian_id,
                    'nomor' => $item['no'],
                ],
                [
                    'deskripsi' => $this->create_deskripsi($images, $item['soal']),
                ]
            );
            //Jawaban::where('soal_id', $soal->soal_id)->delete();
            Jawaban::updateOrCreate(
                [
                    'soal_id' => $soal->soal_id,
                    'urut' => 1,
                    'opsi' => 'A',
                    'benar' => (strtolower($item['kunci']) == 'a') ? 1 : 0,
                ],
                [
                    'deskripsi' => $this->create_deskripsi($images, $item['a']),
                ]
            );
            Jawaban::updateOrCreate(
                [
                    'soal_id' => $soal->soal_id,
                    'urut' => 2,
                    'opsi' => 'B',
                    'benar' => (strtolower($item['kunci']) == 'b') ? 1 : 0,
                ],
                [
                    'deskripsi' => $this->create_deskripsi($images, $item['b']),
                ]
            );
            Jawaban::updateOrCreate(
                [
                    'soal_id' => $soal->soal_id,
                    'urut' => 3,
                    'opsi' => 'C',
                    'benar' => (strtolower($item['kunci']) == 'c') ? 1 : 0,
                ],
                [
                    'deskripsi' => $this->create_deskripsi($images, $item['c']),
                ]
            );
            Jawaban::updateOrCreate(
                [
                    'soal_id' => $soal->soal_id,
                    'urut' => 4,
                    'opsi' => 'D',
                    'benar' => (strtolower($item['kunci']) == 'd') ? 1 : 0,
                ],
                [
                    'deskripsi' => $this->create_deskripsi($images, $item['d']),
                ]
            );
        });
    }
}
