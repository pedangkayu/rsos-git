<?php

namespace App\Jobs\Personalia;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

use App\Models\data_kinerja;
use App\Models\data_kinerja_log;
use App\Models\ref_penilaian;

class KinerjaJob extends Job implements SelfHandling
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
        $head = $this->req['head'];
        $kusioner = $this->req['kusioner'];
        
        $kinerja = data_kinerja::create([
            'id_karyawan' => $head['id_karyawan'],
            'id_penilai' => 1,
            ]);
        foreach ($kusioner as $key => $value) {
          data_kinerja_log::create([
            'id_kinerja' => $kinerja->id,
            'id_penilaian' => $value["'id_penilaian'"],
            'id_penilaian_uraian' => $value["'id_penilaian_uraian'"],
            'score' => isset($value["'score'"]) ? $value["'score'"] : null,//$this->req['nilai_'.$this->req['no'][$i]],
            // 'point' => $this->req['point'][$i]
            ]);
        }
      return $kinerja->id;
    }
}
