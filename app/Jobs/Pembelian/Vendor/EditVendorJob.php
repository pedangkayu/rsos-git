<?php

namespace App\Jobs\Pembelian\Vendor;

use App\Models\data_vendor;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

class EditVendorJob extends Job implements SelfHandling
{
   public $req;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $req) {
        $this->req = $req;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $v = data_vendor::find($this->req['id_vendor']);
        $v->update([
            'nm_vendor' => $this->req['nm_vendor'],
            'pemilik' => $this->req['nama_pemilik'],
            'alamat' => $this->req['alamat'],
            'telpon' => $this->req['telpon'],
            'fax' => $this->req['fax'],
            'email' => $this->req['email'],
            'website' => $this->req['website']
        ]);

        \Loguser::create('Melakukan perubahan terhadap data Penyedia Kode. ' . $v->kode);


        return $this->req;
        
    }
}
