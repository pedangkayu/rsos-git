<?php

namespace App\Jobs\Pengadaan\ReturGudang;

use App\Models\data_retur;
use App\Models\data_barang;
use App\Models\data_log_barang;
use App\Models\data_retur_item;
use App\Models\data_item_gudang;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

class CreateReturGudangJob extends Job implements SelfHandling {

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
                    'tipe' => 1,
                    'id_gudang_asal' => $this->req['id_gudang'],
                    'id_karyawan' => $me
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
                            'kondisi' => 1,
                            'tipe' => 4,
                            'id_parent' => $retur->id_retur,
                            'id_karyawan' => $me
                        ]);

                        // Log Stok
                        data_log_barang::create([
                            'id_barang' => $id_barang,
                            'qty' => $this->req['qty'][$i],
                            'id_gudang' => $this->req['id_gudang'],
                            'kondisi' => 2,
                            'tipe' => 4,
                            'id_parent' => $retur->id_retur,
                            'id_karyawan' => $me
                        ]);

                        // Update stok Gudang besat
                        $barang = data_barang::find($id_barang);
                        $barang->in = $barang->in + $this->req['qty'][$i];
                        $barang->save();

                        // Update Stok sub gudang
                        $gudang = data_item_gudang::where('id_barang', $id_barang)
                            ->where('id_gudang', $this->req['id_gudang'])
                            ->first();

                        $gd = data_item_gudang::find($gudang->id_item_gudang);
                        $gd->out = $gudang->out + $this->req['qty'][$i];
                        $gd->save();

                    endif;
                }  

                $format = 'IN-RTN-';
                $kode = $format . \Format::code($retur->id_retur);
                $retur->no_retur = $kode;
                $retur->save();

                \Loguser::create('Membuat Retur Gudang dengan No. ' . $kode);

            \DB::commit();

            return [
                'result' => true,
                'label' => 'success',
                'err' => 'Return Gudang berhasil dibuat dengan No. ' . $kode
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
