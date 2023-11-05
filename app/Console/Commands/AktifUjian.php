<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pembelajaran;
use App\Models\Ujian;

class AktifUjian extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aktif:ujian';

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
        Ujian::whereNotNull('pembelajaran_id')->update(['status' => 0]);
        Ujian::where(function($query) use ($satuan){
            $query->whereHas('pembelajaran', function($query) use ($satuan){
                $query->where('nama_mata_pelajaran', $satuan);
            });
        })->update(['status' => 1]);
        $this->info('Mata Ujian '.$satuan.' berhasil diaktifasi');
    }
}
