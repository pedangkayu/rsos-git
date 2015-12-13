<?php

namespace App\Jobs\Pembelian\SPH;

use App\Models\data_po;
use App\Models\data_prq;
use App\Models\data_prq_item;
use App\Models\data_po_item;
use App\Models\data_harga;
use App\Models\data_barang;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

class POfromSPHJob extends Job implements SelfHandling
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

            //dd($this->req);

            \DB::begintransaction();

            $me = \Me::data()->id_karyawan;

            // Create PO
            $po = data_po::create([
                'id_sph'        => $this->req['id'],
                'id_vendor'     => $this->req['vendor'],
                'deadline'      => $this->req['deadline'],
                'id_pembuat'    => $me,
                'id_acc'        => 0,
                'adjustment'    => $this->req['adjustment'],
                'diskon'        => $this->req['gdiskon'],
                'ppn'           => $this->req['gppn'],
                // 'pph'           => $this->req['gpph'],
                'keterangan'    => $this->req['ket']
            ]);

            foreach($this->req['id_barang'] as $i => $id_barang){
                data_po_item::create([
                    'id_po'     => $po->id_po,
                    'id_item'   => $id_barang,
                    'req_qty'   => $this->req['qty'][$i],
                    'qty'       => $this->req['qty'][$i],
                    'harga'     => $this->req['harga'][$i],
                    'diskon'    => $this->req['diskon'][$i],
                    // 'ppn'       => $this->req['ppn'][$i],
                    // 'pph'       => $this->req['pph'][$i],
                    'keterangan'=> $this->req['kets'][$i],
                    'id_satuan' => $this->req['satuan'][$i],
                    'id_prq'    => $this->req['id_prq'][$i]
                ]);

                data_prq::find($this->req['id_prq'][$i])->update([
                    'status' => 2
                ]);

                // update Harga
                data_barang::find($id_barang)->update([
                    'harga_beli' => $this->req['harga'][$i]
                ]);

                // Update Log harga
                data_harga::create([
                    'id_barang' => $id_barang,
                    'harga' => $this->req['harga'][$i],
                    'keterangan' => 'Update berdasarkan PO',
                    'id_po' => $po->id_po,
                    'id_karyawan' => $me,
                    'tipe' => 1
                ]);

                data_prq_item::find($this->req['id_prq_item'][$i])->update([
                    'status' => 2
                ]);

            }

            $format = 'PO-';
            $po->no_po = $format . \Format::code($po->id_po);
            $po->save();

            \Loguser::create('Membuat PO dari Modul SPH dengan No. PO ' . $po->no_po);
            \DB::commit();

            return [
                'result' => true,
                'err' => 'PO berhasil dibuat dengan No. ' . $po->no_po
            ];

        }catch(\Exception $e){
            \DB::rollback();

            return [
                'result' => false,
                'err' => $e->getMessage()
            ];
        }
        

    }
}
