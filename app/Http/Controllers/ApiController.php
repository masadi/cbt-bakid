<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Rombongan_belajar;
use App\Models\Ujian;
use App\Models\Guru;
use App\Models\Peserta_didik;
use App\Models\Anggota_rombel;
use App\Models\Mata_pelajaran;
use App\Models\User;
use Rap2hpoutre\FastExcel\FastExcel;
use Validator;
use File;

class ApiController extends Controller
{
    public function index(){
        $data = [
            'user' => $this->loggedUser(),
            'rombel' => Rombongan_belajar::whereHas('anggota_rombel', function($query){
                $query->where('peserta_didik_id', $this->loggedUser()->peserta_didik_id);
            })->first(),
            'ujian' => Ujian::withCount([
                'soal', 
                'jawaban_siswa' => function($query){
                $query->whereHas('user', function($query){
                    $query->where('peserta_didik_id', $this->loggedUser()->peserta_didik_id);
                });
            }])->with([
                'pembelajaran',
                'ujian_siswa' => function($query){
                    $query->where('user_id', $this->loggedUser()->user_id);
                },
            ])->whereHas('pembelajaran', function($query){
                $query->whereHas('rombongan_belajar', function($query){
                    $query->whereHas('anggota_rombel', function($query){
                        $query->where('peserta_didik_id', $this->loggedUser()->peserta_didik_id);
                    });
                });
            })->orderBy('status', 'desc')->get(),
        ];
        return response()->json($data);
    }
    private function loggedUser(){
        return auth()->user();
    }
    public function upload(){
        $validator = Validator::make(request()->all(), 
            [
                'file' => 'required|mimes:xlsx',
            ],
            [
                'file.required' => 'File excel tidak boleh kosong',
                'file.mimes' => 'File excel harus berextensi .xlsx',
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'success' => FALSE,
                'errors' => $validator->errors(),
            ]);
        }
        $upload_path = request()->file->store('public/excel');
        $string = Str::of($upload_path)->basename();
        $filepath = public_path('storage/excel/'.$string);
        $error = NULL;
        $inserted = 0;
        $aborted = 0;
        if(request()->data == 'guru'){
            $text = ' Data Guru';
        } 
        if(request()->data == 'pd'){
            $text = ' Data Peserta Didik';
        }
        if(request()->data == 'mapel'){
            $text = ' Data Mata Pelajaran';
        }
        $collection = (new FastExcel)->import($filepath, function ($line) use (&$inserted, &$aborted, &$error){
            $insert = NULL;
            try {
                if(request()->data == 'guru'){
                    $insert = Guru::updateOrCreate([
                        'nama' => $line['NAMA LENGKAP'],
                        'nip' => $line['NIP'],
                        'nuptk' => $line['NUPTK'],
                        'jenis_kelamin' => $line['L/P'],
                        'email' => $line['EMAIL'],
                    ]);
                    $user = User::firstOrCreate(
                        [
                            'username' => ($line['NUPTK']) ? $line['NUPTK'] : $line['email'],
                            'email' => $line['EMAIL'],
                        ],
                        [
                            'guru_id' => $insert->guru_id,
                            'name' => $line['NAMA LENGKAP'],
                            'password' => bcrypt('12345678'),
                        ]
                    );
                    $user->attachRole('guru');
                }
                if(request()->data == 'pd'){
                    //nama	nisn	kelas	tingkat	tempat_lahir	tanggal_lahir
                    $insert = Peserta_didik::updateOrCreate([
                        'nama' => $line['nama'],
                        'jenis_kelamin' => $line['jenis_kelamin'],
                        'nisn' => $line['nisn'],
                        'tempat_lahir' => $line['tempat_lahir'],
                        'tanggal_lahir' => $line['tanggal_lahir'],
                    ]);
                    $users = User::where('username', $line['nisn'])->count();
                    $random = Str::random(10);
                    $username = ($users) ? $random : $line['nisn'];
                    $user = User::firstOrCreate(
                        [
                            'username' => $username,
                            'email' => $username.'@email.com',
                        ],
                        [
                            'peserta_didik_id' => $insert->peserta_didik_id,
                            'name' => $line['nama'],
                            'password' => bcrypt('12345678'),
                        ]
                    );
                    $user->attachRole('siswa');
                    $rombongan_belajar = Rombongan_belajar::updateOrCreate([
                        'nama' => $line['kelas'],
                        'tingkat' => $line['tingkat'],
                    ]);
                    Anggota_rombel::updateOrCreate([
                        'rombongan_belajar_id' => $rombongan_belajar->rombongan_belajar_id,
                        'peserta_didik_id' => $insert->peserta_didik_id,
                    ]);
                }
                if(request()->data == 'mapel'){
                    $insert = Mata_pelajaran::updateOrCreate([
                        'nama' => $line['nama_mata_pelajaran'],
                    ]);
                }
            } catch (\Throwable $th) {
                $error = $th->getMessage();
            }
            
            if($insert){
                $inserted++;
            } else {
                $aborted++;
            }
        });
        File::delete($filepath);
        $inserted_text = $inserted . $text. ' berhasil disimpan';
        $aborted_text = $aborted . $text. ' gagal disimpan';
        if($error){
            $data = [
                'icon' => 'error',
                'text' => $error,
                'title' => 'Gagal',
            ];    
        } else {
            $data = [
                'icon' => 'success',
                'text' => 'Import excel berhasil di proses<br>'. $inserted_text .'<br>'. $aborted_text,
                'title' => 'Berhasil',
            ];    
        }
        return response()->json($data);
    }
}
