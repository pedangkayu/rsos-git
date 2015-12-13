<?php

namespace App\Jobs\Pengadaan\SubGudang;

use App\Models\data_item_gudang;
use App\Models\data_log_barang;
use App\Models\data_penyesuaian_stok;
use App\Models\data_penyesuaian_stok_item;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

class CreateAdjustemtJob extends Job implements SelfHandling {

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
            
            $stok = data_penyesuaian_stok::create([
                'tipe' => $this->req['tipe'],
                'id_karyawan' => $me,
                'keterangan' => $this->req['ket'],
                'tanggal' => $this->req['tanggal'],
                'id_gudang' => $this->req['id_gudang'],
            ]);

            if(count($this->req['id_barang']) == 0)
                throw new \Exception("Barang/Obat tidak ditemukan!", 1);
            
            $notempty = [];
            foreach($this->req['id_barang'] as $i => $id_barang){
                if(!empty($this->req['qty'][$i])){
                    $notempty[] = 1;
                    $qty = \Format::convertSatuan($id_barang, $this->req['satuan'][$i], $this->req['satuan_default'][$i]) * $this->req['qty'][$i];
                    data_penyesuaian_stok_item::create([
                        'id_penyesuaian_stok'   => $stok->id_penyesuaian_stok,
                        'id_barang'             => $id_barang,
                        'id_satuan'             => $this->req['satuan'][$i],
                        'qty_lg'                => $this->req['qty'][$i],
                        'current_qty'           => $this->req['current_qty'][$i],
                        'new_qty'               => $qty,
                        'keterangan'            => $this->req['kets'][$i]
                    ]);

                }

                if(count($notempty) == 0)
                throw new \Exception("Barang/Obat tidak ditemukan!", 1);

               // Log Stok
                $kondisi = $qty >= $this->req['current_qty'][$i] ? 1 : 2;
                $stok_qty = $this->req['current_qty'][$i] - $qty;
                data_log_barang::create([
                    'id_barang' => $id_barang,
                    'qty' => abs($stok_qty),
                    'keterangan' => 'Penyesuaian Stok',
                    'id_gudang' => $this->req['id_gudang'],
                    'kondisi' => $kondisi,
                    'tipe' => 3,
                    'id_parent' => $stok->id_penyesuaian_stok,
                    'id_karyawan' => $me,
                ]);

                // Stok gudang
                $barang = data_item_gudang::where('id_barang', $id_barang)->where('id_gudang', $this->req['id_gudang'])->first();
                $brg = data_item_gudang::find($barang->id_item_gudang);
                if($kondisi == 1)
                    $brg->in = $barang->in + abs($stok_qty);
                else
                    $brg->out = $barang->out + abs($stok_qty);
                
                $brg->save();
            }

            // Code
            $tipe = 'O-';
            $format = $tipe . 'ADJ-SG-' . \Format::code($stok->id_penyesuaian_stok);
            $stok->no_penyesuaian_stok = $format;
            $stok->save();

            \Loguser::create('Melakukan penyesuaian Stok dengan No. ' . $stok->no_penyesuaian_stok);

            \DB::commit();

            return [
                'result' => true,
                'label' => 'success',
                'err' => 'Penyesuaian berhasil di buat dengan No. ' . $stok->no_penyesuaian_stok
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
