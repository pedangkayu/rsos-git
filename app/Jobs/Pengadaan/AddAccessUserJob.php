<?php

namespace App\Jobs\Pengadaan;

use App\Models\data_akses_gudang;
use App\User;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

class AddAccessUserJob extends Job implements SelfHandling
{
    public $req;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $req){
        $this->req = $req;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        
        $user = data_akses_gudang::firstOrCreate([
            'id_user' => $this->req['user']
        ]);

        $user->tipe = $this->req['tipe'];
        $user->save();

        $u = User::find($this->req['user']);
        $tipe = $this->req['tipe'] == 1 ? 'Obat' : 'Barang';
        \Loguser::create('Memberikan Akses Gudang ' . $tipe . ' kepada ' . $u->name);

        return $user;

    }
}
