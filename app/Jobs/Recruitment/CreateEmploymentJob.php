<?php

namespace App\Jobs\Recruitment;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

use App\Models\data_employment;
use App\Models\data_employment_portfolio;

class CreateEmploymentJob extends Job implements SelfHandling
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
      
      
      $data = data_employment::create([
        'id_recruitment' => $this->req['id_recruitment'],
        'nm_depan' => $this->req['nm_depan'],
        'nm_belakang' => $this->req['nm_belakang'],
        'mobile' => $this->req['telp'],
        'email' => $this->req['email'],
        'sex' => $this->req['gender'],
        'tempat_lahir' => $this->req['tempat_lahir'],
        'tgl_lahir' => date('Y-m-d',strtotime($this->req['tgl_lahir'])),
        'alamat' => $this->req['alamat'],
        'agama' => $this->req['agama'],
        'pendidikan' => $this->req['pendidikan'],
        'id_status' => 1
        ]);

      $id = $data->id;
      if(isset($this->req['company_name']) && count($this->req['company_name']) > 0){
        foreach ($this->req['company_name'] as $i => $value) {
          data_employment_portfolio::create([
            'id_employment' => $id,
            'company_name' => $this->req['company_name'][$i],
            'title' => $this->req['title'][$i],
            'location' => $this->req['location'][$i],
            'date_start' => date('Y-m-d',strtotime($this->req['date_start'][$i])),
            'date_end' => date('Y-m-d',strtotime($this->req['date_end'][$i])),
            'description' => $this->req['description'][$i]
            ]);
        }
      }
    }
  }
