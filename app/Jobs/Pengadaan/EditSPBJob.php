<?php

namespace App\Jobs\Pengadaan;


use App\Models\data_spb;
use App\Models\data_spb_item;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

class EditSPBJob extends Job implements SelfHandling{

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
            $spb->keterangan = $this->req['ket'];
            $spb->deadline = date('Y-m-d', strtotime($this->req['deadline']));
            $spb->save();

            data_spb_item::where('id_spb', $this->req['id_spb'])->delete();

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

            \Loguser::create('Melakukan perubahan terhadap data PMB/PMO No. ' . $spb->no_spb);

            \DB::commit();

            return [
                'label' => 'success',
                'err' => 'Sukses, No ' . $spb->no_spb . ' berhasil diperbaharui.'
            ];

        }catch(\Exception $e){
            \DB::rollback();
            
             return [
                'label' => 'danger',
                'err' => $e->getMessage()
            ];
        }

        

    }
}
