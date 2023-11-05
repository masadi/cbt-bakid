<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Models\User;
use App\Models\Peserta_didik;
use App\Models\Anggota_rombel;
use App\Models\Rombongan_belajar;
use App\Models\Role;

class GenerateSiswa extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:siswa';

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
        $folder = public_path('templates');
        $role = Role::where('name', 'siswa')->first();
        $users = (new FastExcel)->import($folder.'/pd.xlsx', function ($item) use ($role){
            $rombel = Rombongan_belajar::updateOrCreate([
                'nama' => $item['kelas'],
                'tingkat' => $item['tingkat'],
            ]);
            $pd = Peserta_didik::updateOrCreate(
                [
                    'nisn' => $item['nisn'],
                ],
                [
                    'nama' => $item['nama'],
                    'tempat_lahir' => $item['tempat_lahir'],
                    'tanggal_lahir' => $item['tanggal_lahir'],
                ]
            );
            $find = Rombongan_belajar::where('nama', $item['kelas'])->first();
            Anggota_rombel::updateOrCreate(
                [
                    'peserta_didik_id' => $pd->peserta_didik_id,
                    'rombongan_belajar_id' => $find->rombongan_belajar_id,   
                ]
            );
            $user = User::updateOrCreate(
                [
                    'username' => $item['nisn'],
                ],
                [
                    'name' => $item['nama'],
                    'email' => $item['nisn'].'@email.com',
                    'password' => bcrypt('12345678'),
                    'peserta_didik_id' => $pd->peserta_didik_id,
                ]
            );
            if(!$user->hasRole('siswa')){
                $user->attachRole($role);
            }
        });
    }
}
