<?php

namespace App\Jobs\Pembelian\ReturVendor;

use App\Models\data_po;
use App\Models\data_retur;
use App\Models\data_barang;
use App\Models\data_po_item;
use App\Models\data_log_barang;
use App\Models\data_retur_item;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

class CreateReturJob extends Job implements SelfHandling {

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

                $retur = data_retur::create([
                    'tipe' => 2,
                    'id_po' => $this->req['id_po'],
                    'id_vendor' => $this->req['id_vendor'],
                    'id_karyawan' => $me
                ]);

                data_po::find($this->req['id_po'])->update([
                    'status' => 2
                ]);

                foreach($this->req['id_barang'] as $i => $id_barang){

                    if(!empty($this->req['qty'][$i])):

                        data_retur_item::create([
                            'id_retur' => $retur->id_retur,
                            'id_barang' => $id_barang,
                            'id_satuan' => $this->req['id_satuan'][$i],
                            'qty' => $this->req['qty'][$i],
                            'qty_lg' => $this->req['qty'][$i],
                        ]);

                        // Log Stok
                        data_log_barang::create([
                            'id_barang' => $id_barang,
                            'qty' => $this->req['qty'][$i],
                            'id_gudang' => 0,
                            'kondisi' => 2,
                            'tipe' => 5,
                            'id_parent' => $retur->id_retur,
                            'id_karyawan' => $me
                        ]);

                        // Update stok
                        $barang = data_barang::find($id_barang);
                        $barang->out = $barang->out + $this->req['qty'][$i];
                        $barang->save();

                        // Update data PO items
                        $itm = data_po_item::where('id_po', $this->req['id_po'])->where('id_item', $id_barang)->first();
                        $qpo = $itm->qty + $this->req['qty'][$i];
                        data_po_item::where('id_po', $this->req['id_po'])->where('id_item', $id_barang)->update([
                            'qty'       => $qpo,
                            'status'    => 1
                        ]);

                    endif;
                }  

                $format = 'EX-RTN-';
                $kode = $format . \Format::code($retur->id_retur);
                $retur->no_retur = $kode;
                $retur->save();

                \Loguser::create('Membuat Retur Pembelian dengan No. ' . $kode);

            \DB::commit();

            return [
                'result' => true,
                'label' => 'success',
                'err' => 'Return Pembelian berhasil dibuat dengan No. ' . $kode
            ];

        }catch(\Exception $e){
            \DB::rollback();            
            return [
                'result' => false,
                'label' => 'danger',
                'err' => $e->getMessage()
            ];
        }

    }
}
