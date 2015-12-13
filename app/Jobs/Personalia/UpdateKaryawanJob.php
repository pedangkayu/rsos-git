<?php

namespace App\Jobs\Personalia;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

use App\Models\data_karyawan;

class UpdateKaryawanJob extends Job implements SelfHandling
{
    public $req;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $req)
    {
        $this->req = $req;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        data_karyawan::find($this->req['id'])
        ->update([
            'NIK' => $this->req['nik'],
            'nm_depan' => $this->req['nm_depan'],
            'nm_belakang' => $this->req['nm_belakang'],
            'telp' => $this->req['telp'],
            'email' => $this->req['email'],
            'sex' => $this->req['gender'],
            'hp' => $this->req['hp'],
            'tempat_lahir' => $this->req['tempat_lahir'],
            'tgl_lahir' => date('Y-m-d',strtotime($this->req['tgl_lahir'])),
            'jabatan' => $this->req['jabatan'],
            'alamat' => $this->req['alamat'],
            'agama' => $this->req['agama'],
            'pendidikan' => $this->req['pendidikan'],
            'id_status' => 1,
            'tgl_bergabung' => date('Y-m-d',strtotime($this->req['tgl_bergabung'])),
            'id_departemen' => $this->req['id_departemen'],
            ]);

    return $this->req;
    }
}
