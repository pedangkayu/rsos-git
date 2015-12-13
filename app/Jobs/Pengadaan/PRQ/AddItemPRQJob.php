<?php

namespace App\Jobs\Pengadaan\PRQ;

use App\Models\data_prq;
use App\Models\data_prq_item;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

class AddItemPRQJob extends Job implements SelfHandling
{
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
    public function handle() {
        
        foreach($this->req['id_barang'] as $i => $id_barang){
            if(!empty($this->req['qty'][$i])){
                $item = data_prq_item::firstOrCreate([
                    'id_prq' => $this->req['id_prq'],
                    'id_barang' => $id_barang,
                ]);

                $item->update([
                    'qty' => $this->req['qty'][$i],
                    'keterangan' => $this->req['kets'][$i],
                    'id_satuan' => $this->req['satuan'][$i]
                ]);
            }
                
        }

        $prq = data_prq::find($this->req['id_prq']);

        \Loguser::create('Menambahkan item tambahan terhadap PRQ No. ' . $prq->no_prq);
        return $this->req;

    }
}
