<?php

namespace App\Jobs\Pembelian\PO;

use App\Models\data_po;
use App\Models\data_po_item;
use App\Models\data_harga;
use App\Models\data_barang;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

class EditPOJob extends Job implements SelfHandling
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
            $po = data_po::find($this->req['id']);
            $po->update([
                'id_vendor'     => $this->req['vendor'],
                'deadline'      => $this->req['deadline'],
                'adjustment'    => $this->req['adjustment'],
                'diskon'        => $this->req['gdiskon'],
                'ppn'           => $this->req['gppn'],
                // 'pph'           => $this->req['gpph'],
                'keterangan'    => $this->req['ket']
            ]);

            data_po_item::whereId_po($this->req['id'])->delete();
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
                    'keterangan'    => $this->req['kets'][$i],
                    'id_satuan' => $this->req['satuan'][$i]
                ]);

                // update Harga
                data_barang::find($id_barang)->update([
                    'harga_beli' => $this->req['harga'][$i]
                ]);

                // Update Log harga
                data_harga::create([
                    'id_barang' => $id_barang,
                    'harga_beli' => $this->req['harga'][$i],
                    'keterangan' => 'Update berdasarkan pembaharuan PO',
                    'id_po' => $po->id_po,
                    'id_karyawan' => $me
                ]);
            }

            \Loguser::create('Melakukan perubahan data PO No. ' . $po->no_po);
            \DB::commit();

            return [
                'result' => true,
                'err' => 'PO berhasil diperbaharui'
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
