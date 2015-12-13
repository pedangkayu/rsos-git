<?php

namespace App\Jobs\Personalia;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

use App\Models\data_karyawan;
use App\Models\data_karyawan_klrg;

class UpdateKeluargaJob extends Job implements SelfHandling
{
    public $req;
    /**
     * Create a new job instance.
     * @author yoga@valdlabs.com
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
        data_karyawan_klrg::find($this->req['id'])
        ->update([
            'id_karyawan' => $this->req['id_karyawan'],
            'nm_depan' => $this->req['nm_depan'],
            'nm_belakang' => $this->req['nm_belakang'],
            'hubungan' => $this->req['hubungan'],
            'sex' => $this->req['gender'],
            'tempat_lahir' => $this->req['tempat_lahir'],
            'tgl_lahir' => date('Y-m-d',strtotime($this->req['tgl_lahir'])),
            'pendidikan' => $this->req['pendidikan'],
            'pekerjaan' => $this->req['pekerjaan'],
            ]);

        return $this->req;
    }
}
