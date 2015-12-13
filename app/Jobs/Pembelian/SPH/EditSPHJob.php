<?php

namespace App\Jobs\Pembelian\SPH;

use App\Models\data_sph;
use App\Models\data_sph_grup;
use App\Models\data_sph_item;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

class EditSPHJob extends Job implements SelfHandling
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
    public function handle() {

        try{

            \DB::begintransaction();

            $sph = data_sph::find($this->req['id']);

            $grup   = data_sph_grup::whereId_sph_grup($sph->id_sph_grup)->select('status')->first();
            if($grup->status == 2)
                throw new \Exception("Maaf SPH No. " . $sph->no_sph_item . " sudah kadaluarsa!", 1);

            $sph->update([
                'id_vendor'     => $this->req['vendor'],
                'deadline'      => $this->req['deadline'],
                'diskon'        => $this->req['gdiskon'],
                'ppn'           => $this->req['gppn'],
                // 'pph'           => $this->req['gpph'],
                'adjustment'    => $this->req['adjustment'],
                'keterangan'    => $this->req['ket']
            ]);

            data_sph_item::whereId_sph($this->req['id'])->delete();
            foreach($this->req['id_sph_item'] as $i => $id_sph_item){
                data_sph_item::create([
                    'id_sph'    => $this->req['id'],
                    'id_prq'    => $this->req['id_prq'][$i],
                    'id_item'   => $this->req['id_barang'][$i],
                    'qty'       => $this->req['qty'][$i],
                    'harga'     => $this->req['harga'][$i],
                    'diskon'    => $this->req['diskon'][$i],
                    // 'ppn'       => $this->req['ppn'][$i],
                    // 'pph'       => $this->req['pph'][$i],
                    'keterangan' => $this->req['kets'][$i],
                    'id_satuan' => $this->req['satuan'][$i]
                ]);
            } 

            \Loguser::create('Memperbaharui data SPH No. ' . $sph->no_sph_item);
            \DB::commit();

            return [
                'status'    => true,
                'err'       => $sph
            ];

        }catch(\Exception $e){
            \DB::rollback();

            return [
                'status'    => false,
                'err'       => $e->getMessage()
            ];
        }
    }
}
