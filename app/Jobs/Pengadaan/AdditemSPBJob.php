<?php

namespace App\Jobs\Pengadaan;

use App\Models\data_spb;
use App\Models\data_spb_item;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

class AdditemSPBJob extends Job implements SelfHandling{
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

            $spb = data_spb::find($this->req['id_spb']);

            $item = data_spb_item::whereId_spb($this->req['id_spb'])->select('id_gudang')->first();

            $gudang = $item->id_gudang;
            foreach($this->req['id_barang'] as $i => $id){
                if(!empty($this->req['qty'][$i])){
                    $items = data_spb_item::firstOrCreate([
                        'id_spb' => $this->req['id_spb'],
                        'id_item' => $id,
                        'status' => 1
                    ]);

                    $qty = \Format::convertSatuan($id, $this->req['satuan'][$i], $this->req['id_satuan'][$i]) * $this->req['qty'][$i];

                    $items->update([
                        'id_gudang' => $gudang,
                        'qty_awal' => $qty,
                        'qty' => $qty,
                        'qty_lg' => $this->req['qty'][$i],
                        'keterangan' => $this->req['kets'][$i],
                        'id_satuan' => $this->req['satuan'][$i]
                    ]);

                }
            }

            \Loguser::create('Menambahkan item tambahan terhadap PMB/PMO No. ' . $spb->no_spb);

            \DB::commit();

            return [
                'res' => true,
                'label' => 'success',
                'err' => $spb->no_spb . ' berhasil ditambahkan!'
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
