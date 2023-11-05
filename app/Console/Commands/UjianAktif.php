<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ujian;

class UjianAktif extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ujian:aktif';

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
        Ujian::whereNotNull('pembelajaran_id')->update(['status' => 1]);
        $this->info('Mata Ujian berhasil diaktifasi');
    }
}
