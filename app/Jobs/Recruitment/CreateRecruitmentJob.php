<?php

namespace App\Jobs\Recruitment;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

use App\Models\data_recruitment;

class CreateRecruitmentJob extends Job implements SelfHandling
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
        data_recruitment::create([
            'posisi' => $this->req['posisi'],
            'date_open' => date('Y-m-d',strtotime($this->req['date_open'])),
            'date_close' => date('Y-m-d',strtotime($this->req['date_close'])),
            'syarat' => $this->req['syarat'],
            'jobdesk' => $this->req['job_desk'],
        //    'gambar' => $this->req['gambar'],
            'estimasi_gaji' => $this->req['estimasi_gaji'],
            'catatan' => $this->req['catatan'],
            'status' => 1
            ]);

        return $this->req;
    }
}
