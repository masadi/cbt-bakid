<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Peserta_didik;
use App\Models\Pembelajaran;
use App\Models\Ujian;
use Rap2hpoutre\FastExcel\FastExcel;

class GenerateHasil extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:hasil';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $data = Pembelajaran::orderBy('mata_pelajaran_id')->get();
        $list_data = [];
        foreach($data as $d){
            $list_data[$d->mata_pelajaran_id] = $d->nama_mata_pelajaran;
        }
        $satuan = $this->choice(
            'Pilih Mapel untuk di aktifasi!',
            $list_data
        );
        $siswa = Peserta_didik::whereHas('jawaban_siswa', function($query) use ($satuan){
            $query->whereHas('soal', function($query) use ($satuan){
                $query->whereHas('ujian', function($query) use ($satuan){
                    $query->whereHas('pembelajaran', function($query) use ($satuan){
                        $query->where('nama_mata_pelajaran', $satuan);
                    });
                });
            });
        })->with(['jawaban_siswa' => function($query) use ($satuan){
            $query->with(['jawaban']);
            $query->whereHas('soal', function($query) use ($satuan){
                $query->whereHas('ujian', function($query) use ($satuan){
                    $query->whereHas('pembelajaran', function($query) use ($satuan){
                        $query->where('nama_mata_pelajaran', $satuan);
                    });
                });
            });
        }])->withCount([
            'jawaban_siswa as benar' => function($query) use ($satuan){
                $query->whereHas('jawaban', function($query) use ($satuan){
                    $query->where('benar', 1);
                });
                $query->whereHas('soal', function($query) use ($satuan){
                    $query->whereHas('ujian', function($query) use ($satuan){
                        $query->whereHas('pembelajaran', function($query) use ($satuan){
                            $query->where('nama_mata_pelajaran', $satuan);
                        });
                    });
                });
            },
            'jawaban_siswa as salah' => function($query) use ($satuan){
                $query->whereHas('jawaban', function($query) use ($satuan){
                    $query->where('benar', 0);
                });
                $query->whereHas('soal', function($query) use ($satuan){
                    $query->whereHas('ujian', function($query) use ($satuan){
                        $query->whereHas('pembelajaran', function($query) use ($satuan){
                            $query->where('nama_mata_pelajaran', $satuan);
                        });
                    });
                });
            },
        ])->orderBy('nama')->get();
        if($siswa->count()){
            $result = [];
            $data = [];
            $i=1;
            foreach($siswa as $pd){
                $data['NO'] = $i;
                $data['NAMA'] = $pd->nama;
                $data['NISN'] = $pd->nisn;
                $jawaban_siswa = $pd->jawaban_siswa->sortBy(function($query){
                   return $query->jawaban->soal->nomor; 
                });
                //dd($jawaban_siswa);
                foreach($jawaban_siswa as $jawaban){
                    $data[$jawaban->jawaban->soal->nomor] = $jawaban->opsi;
                    //dump($jawaban_siswa);
                }
                $nilai = ($pd->benar*10)/4;
                $data['BENAR'] = $pd->benar;
                $data['SALAH'] = $pd->salah;
                $data['NILAI'] = number_format($nilai, 0, '.', '.');
                $result[] = $data;
                $i++;
            }
            $this->info($satuan.' berhasil di generate');
            return (new FastExcel($result))->export(public_path('downloads/'.$satuan.'.xlsx'));
        } else {
            $this->error($satuan.' tidak memiliki nilai');
        }
    }
}
