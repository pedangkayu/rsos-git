<?php

namespace App\Jobs\Pembelian\SPH;

use App\Models\data_sph;
use App\Models\data_sph_grup;
use App\Models\data_sph_item;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

class CopySPHJob extends Job implements SelfHandling
{
    public $id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id) {
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {

        try{

            \DB::begintransaction();

            $sph = data_sph::find($this->id);
            $grup   = data_sph_grup::whereId_sph_grup($sph->id_sph_grup)->select('status')->first();
            if($grup->status == 2)
                throw new \Exception("Maaf SPH No. " . $sph->no_sph_item . " sudah kadaluarsa!", 1);

            $nsph = data_sph::create([
                'id_sph_grup'   => $sph->id_sph_grup,
                'id_vendor'     => $sph->id_vendor,
                'deadline'      => $sph->deadline,
                'id_pembuat'    => \Me::data()->id_karyawan,
                'id_acc'        => $sph->id_acc,
                'diskon'        => $sph->diskon,
                'ppn'           => $sph->ppn,
                'pph'           => $sph->pph,
                'adjustment'    => $sph->adjustment,
            ]);
            
            $items = data_sph_item::whereId_sph($this->id)->get();
            foreach($items as $item){
                data_sph_item::create([
                    'id_sph'    => $nsph->id_sph,
                    'id_prq'    => $item->id_prq,
                    'id_item'   => $item->id_item,
                    'qty'       => $item->qty,
                    'harga'     => $item->harga,
                    'diskon'    => $item->diskon,
                    'ppn'       => $item->ppn,
                    'pph'       => $item->pph,
                    'id_satuan' => $item->id_satuan
                ]);
            } 

            $fo = 'ISPH/'; 
            $nsph->no_sph_item = $fo . \Format::code($nsph->id_sph);
            $nsph->save();


            \Loguser::create('Menduplikasi data SPH dari No. ' . $sph->no_sph_item);
            \DB::commit();

            return [
                'err'       => 'SPH berhasil di duplikasi dengan No. ' . $nsph->no_sph_item
            ];

        }catch(\Exception $e){
            \DB::rollback();

            return [
                'err'       => $e->getMessage()
            ];
        }
    }
}
