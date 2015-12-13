<?php

namespace App\Jobs\Users;

use App\User;
use App\Models\data_level;
use App\Models\data_karyawan;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

class CreateUserJob extends Job implements SelfHandling
{
    
    public $req;

    public function __construct(array $req){
        $this->req = $req;
    }

    
    public function handle(){
        $karyawan = data_karyawan::find($this->req['karyawan']);
        $user = User::create([
            'id_karyawan'   => $this->req['karyawan'],
            'name'          => $karyawan->nm_depan . ' ' . $karyawan->nm_belakang,
            'username'      => $this->req['username'],
            'password'      => bcrypt($this->req['password']),
            'permission'    => $this->req['permission']
        ]);
        foreach($this->req['levels'] as $level){
            data_level::firstOrCreate([
                'id_user' => $user->id_user,
                'id_level_user' => $level
            ]);
        }
        return $user;
    }
}
