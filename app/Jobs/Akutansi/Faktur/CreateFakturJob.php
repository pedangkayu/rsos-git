<?php

namespace App\Jobs\Akutansi\Faktur;

use App\Models\data_faktur;
use App\Models\data_faktur_item;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

class CreateFakturJob extends Job implements SelfHandling {

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
        //dd($this->req);
        $me = \Me::data()->id_karyawan;

        try{
            \DB::begintransaction();

            $faktur = data_faktur::create([
                'nomor_type' => $this->req['no_po'],
                'prefix' => $this->req['prefix'],
                'type' => 1,
                'id_vendor' => $this->req['supplier'],
                'id_po' => $this->req['id_po'],
                'tgl_faktur' => date('Y-m-d', strtotime($this->req['tanggal'])),
                'duodate' => date('Y-m-d', strtotime($this->req['duodate'])),
                'id_payment_terms' => $this->req['terms'],
                'ppn' => $this->req['ppn'],
                'diskon' => $this->req['diskon'],
                'adjustment' => $this->req['adjustment'],
                'subtotal' => $this->req['subtotal'],
                'total' => $this->req['grandtotal'],
                'keterangan' => $this->req['keterangan'],
                'status' => 0,
                'id_karyawan' => $me,
            ]);

            foreach ($this->req['id_barang'] as $i => $id) {
                data_faktur_item::create([
                    'id_faktur' => $faktur->id_faktur,
                    'id_item' => $id,
                    'deskripsi' => $this->req['deskripsi'][$i],
                    'qty' => $this->req['qty'][$i],
                    'harga' => $this->req['harga'][$i],
                    'diskon' => $this->req['diskons'][$i],
                    'total' => $this->req['total'][$i],
                    'id_po' => $this->req['id_po'],
                    'id_satuan' => $this->req['id_satuan'][$i],
                ]);
            }

            /* Kode Faktur */
            $no = \Format::code($faktur->id_faktur);
            $faktur->nomor_faktur = $no;
            $faktur->save();

            \DB::commit();

            return [
                'res' => true,
                'label' => 'success',
                'err' => '<center>Faktur berhasil dibuat dengan No. #' . $no . '</center>'
            ];

        }catch(\Exception $e){
            \DB::rollback();

            return [
                'res' => false,
                'label' => 'danger',
                'err' => $e->getMessage()
            ];

        }

    }
}
