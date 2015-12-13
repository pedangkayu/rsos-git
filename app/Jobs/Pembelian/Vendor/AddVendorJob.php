<?php

namespace App\Jobs\Pembelian\Vendor;

use App\Models\data_vendor;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

class AddVendorJob extends Job implements SelfHandling {
    
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
    public function handle() {
        
        $v = data_vendor::create([
            'nm_vendor' => $this->req['nm_vendor'],
            'pemilik' => $this->req['nama_pemilik'],
            'alamat' => $this->req['alamat'],
            'telpon' => $this->req['telpon'],
            'fax' => $this->req['fax'],
            'id_karyawan' => \Me::data()->id_karyawan,
            'email' => $this->req['email'],
            'website' => $this->req['website']
        ]);

        $format = 'VDR-';
        $v->kode = $format . \Format::code($v->id_vendor);
        $v->save();

        \Loguser::create('Menambahkan data Penyedia dengan Kode. ' . $v->kode);

        return $v;
    }
}
