<?php

namespace App\Jobs\Pengadaan\PRQ;

use App\Models\data_prq;
use App\Models\data_prq_item;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

class EditPRQJob extends Job implements SelfHandling
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

        $titipan = (!empty($this->req['titipan'])) ? $this->req['vendor'] : 0;

        $prq = data_prq::find($this->req['id_prq']);
        $prq->update([
            'target' => date('Y-m-d', strtotime($this->req['deadline'])),
            'keterangan' => $this->req['ket'],
            'titipan' => $titipan
        ]);

        data_prq_item::whereId_prq($this->req['id_prq'])->delete();
        foreach($this->req['id_barang'] as $i => $id_barang){
            if(!empty($this->req['qty'][$i]))
                data_prq_item::create([
                    'id_prq' => $this->req['id_prq'],
                    'id_barang' => $id_barang,
                    'qty' => $this->req['qty'][$i],
                    'keterangan' => $this->req['kets'][$i],
                    'id_satuan' => $this->req['satuan'][$i]
                ]);
        }

        \Loguser::create('Melakukan perubahan data PRQ No. ' . $prq->no_prq);

        return $prq;
    }
}
