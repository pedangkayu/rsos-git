<?php

namespace App\Jobs\Pembelian\SPH;

use App\Models\data_po;
use App\Models\data_sph;
use App\Models\data_po_item;
use App\Models\data_sph_item;
use App\Models\data_sph_grup;
use App\Models\data_harga;
use App\Models\data_barang;
use App\Models\data_prq_item;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

class SPHtoPOJob extends Job implements SelfHandling
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

            $me = \Me::data()->id_karyawan;

            $sph = data_sph::find($this->req['id_sph']);

            $grup   = data_sph_grup::whereId_sph_grup($sph->id_sph_grup)->select('status')->first();
            if($grup->status == 2)
                throw new \Exception("Maaf SPH No. " . $sph->no_sph_item . " sudah kadaluarsa!", 1);
            
            // Create PO
            $po = data_po::create([
                'id_sph'        => $this->req['id_sph'],
                'id_vendor'     => $sph->id_vendor,
                'deadline'      => $sph->deadline,
                'id_pembuat'    => $me,
                'id_acc'        => 0,
                'adjustment'    => $sph->adjustment,
                'diskon'        => $sph->diskon,
                'ppn'           => $sph->ppn,
                // 'pph'           => $sph->pph,
                'keterangan'    => $sph->keterangan
            ]);

            $items = data_sph_item::whereId_sph($this->req['id_sph'])->get();
            foreach($items as $item){
                data_po_item::create([
                    'id_po'         => $po->id_po,
                    'id_item'       => $item->id_item,
                    'req_qty'       => $item->qty,
                    'qty'           => $item->qty,
                    'harga'         => $item->harga,
                    'diskon'        => $item->diskon,
                    'ppn'           => $item->ppn,
                    'pph'           => $item->pph,
                    'keterangan'    => $item->keterangan,
                    'id_satuan'     => $item->id_satuan,
                    'id_prq'        => $item->id_prq,
                ]);

                // update Harga
                data_barang::find($item->id_item)->update([
                    'harga_beli' => $item->harga
                ]);

                // Update Log harga
                data_harga::create([
                    'id_barang' => $item->id_item,
                    'harga' => $item->harga,
                    'keterangan' => 'Update berdasarkan PO',
                    'id_po' => $po->id_po,
                    'id_karyawan' => $me,
                    'tipe' => 1
                ]);

                data_prq_item::whereId_prq($item->id_prq)->whereId_barang($item->id_item)->update([
                    'status' => 2
                ]);

            }

            $format = 'PO-';
            $po->no_po = $format . \Format::code($po->id_po);
            $po->save();

            // Update data SPH Grup
            data_sph_grup::find($this->req['id_sph_grup'])->update([
                'status' => 2
            ]);

            // Update data sph
            $sph->status = 2;
            $sph->save();


            \Loguser::create('Menjadikan SPH denga No. ' . $sph->no_sph . ' menjadi PO dengan No. ' . $po->no_po);
            \DB::commit();

            return [
                'err' => 'Po Berhail di buat dengan No. ' . $po->no_po
            ];

        }catch(\Exception $e){
            \DB::rollback();

            return [
                'err' => $e->getMessage()
            ];

        }

    }
}
