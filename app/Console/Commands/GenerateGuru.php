<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Models\Guru;
use App\Models\User;
use App\Models\Role;
use App\Models\Pembelajaran;

class GenerateGuru extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:guru';

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
        $role = Role::where('name', 'guru')->first();
        $users = (new FastExcel)->import($folder.'/ptk.xlsx', function ($d) use ($role){
            $guru = Guru::updateOrCreate(
                [
                    'nuptk' => $d['nuptk']
                ],
                [
                    'nama' => $d['nama'],
                ]
            );
            $user = User::updateOrCreate(
                [
                    'username' => $guru->nuptk,
                ],
                [
                    'name' => $guru->nama,
                    'email' => $d['email'],
                    'password' => bcrypt('12345678'),
                    'guru_id' => $guru->guru_id,
                ]
            );
            if(!$user->hasRole('guru')){
                $user->attachRole($role);
            }
        });
    }
}
