<?php

namespace App\Jobs\Pengadaan;

use App\Models\data_spb;
use App\Models\data_spb_item;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

class CreatePMBJob extends Job implements SelfHandling{

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
    public function handle(){
        

        try{

            \DB::begintransaction();

            $spb = data_spb::create([
                'id_departemen' => \Me::data()->id_departemen,
                'id_pemohon' => \Me::data()->id_karyawan,
                'keterangan' => $this->req['ket'],
                'id_acc' => 0,
                'deadline' => date('Y-m-d', strtotime($this->req['deadline'])),
                'tipe' => $this->req['tipe']
            ]);

            $gudang = empty($this->req['id_gudang']) ? 0 : $this->req['id_gudang'];

            foreach($this->req['id_barang'] as $i => $id){

                if(!empty($this->req['qty'][$i])){

                    $qty = \Format::convertSatuan($id, $this->req['satuan'][$i], $this->req['id_satuan'][$i]) * $this->req['qty'][$i];
                    
                    data_spb_item::create([
                        'id_spb' => $spb->id_spb,
                        'id_item' => $id,
                        'qty_awal' => $qty,
                        'qty' => $qty,
                        'qty_lg' => $this->req['qty'][$i],
                        'keterangan' => $this->req['kets'][$i],
                        'status' => 1,
                        'id_gudang' => $gudang,
                        'id_satuan' => $this->req['satuan'][$i]
                    ]);
                }
                    
            }

            $kode = $this->req['tipe'] == 1 ? 'PMO-' : 'PMB-';
            $spb->no_spb = $kode . \Format::code($spb->id_spb);
            $spb->save();

            \Loguser::create('Membuat Pengajuan barang No. ' . $spb->no_spb);

            \DB::commit();

            return [
                'res' => true,
                'label' => 'success',
                'err' => 'Permohonan Barang berhasil terkirim dengan Nomor ' . $spb->no_spb
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
