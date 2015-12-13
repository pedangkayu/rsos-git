<?php

namespace App\Jobs\Recruitment;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

use App\Models\data_employment;
use App\Models\data_karyawan;

class UpdateEmploymentJob extends Job implements SelfHandling
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
      $rec = data_employment::find($this->req['id']);

      data_employment::find($this->req['id'])
      ->update([
        'id_status' => $this->req['id_status']
        ]);
      if($this->req['id_status'] == 2){
        data_karyawan::create([
          'nm_depan' => $rec->nm_depan,
          'nm_belakang' => $rec->nm_belakang,
          'email' => $rec->email,
          'sex' => $rec->sex,
          'hp' => $rec->mobile,
          'tempat_lahir' => $rec->tempat_lahir,
          'tgl_lahir' => date('Y-m-d',strtotime($rec->tgl_lahir)),
          'jabatan' => 1,
          'alamat' => $rec->alamat,
          'agama' => $rec->agama,
          'pendidikan' => $rec->pendidikan,
          'id_status' => 15,
          'tgl_bergabung' => date('Y-m-d'),
          ]);    
      }else{

      }
      return $this->req;
    }
  }
