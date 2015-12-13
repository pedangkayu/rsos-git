<?php

namespace App\Jobs\Pembelian\SPBM;

use App\Models\data_po;
use App\Models\data_prq;
use App\Models\data_spbm;
use App\Models\data_barang;
use App\Models\data_po_item;
use App\Models\data_prq_item;
use App\Models\data_spbm_item;
use App\Models\data_log_barang;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

class CreateSPBMJob extends Job implements SelfHandling {

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
        
        //dd($this->req);

        try{

            \DB::begintransaction();

            $gr = data_spbm::create([
                'no_surat_jalan'        => $this->req['sj'],
                'tgl_terima_barang'     => $this->req['tgl_terima'],
                'tgl_periksa_barang'    => $this->req['tgl_periksa'],
                'id_kirim'              => $this->req['pengiriman'],
                'keterangan'            => $this->req['keterangan'],
                'id_po'                 => $this->req['id_po'],
                'pemeriksa1'            => $this->req['pemeriksa1'],
                'pemeriksa2'            => $this->req['pemeriksa2'],
                'nm_pengirim'           => $this->req['nm_pengirim'],
                'titipan'               => $this->req['titipan'],
                'id_karyawan'           => \Me::data()->id_karyawan,
            ]);

            $statuspo = [];

            foreach($this->req['id_po_item'] as $i => $id_po_item){
                if(!empty($this->req['qty_lg'])){

                    $qty = \Format::convertSatuan($this->req['id_barang'][$i], $this->req['id_satuan'][$i], $this->req['id_satuan_default'][$i]) * $this->req['qty_lg'][$i];

                    data_spbm_item::create([
                        'id_spbm' => $gr->id_spbm,
                        'id_barang' => $this->req['id_barang'][$i],
                        'bonus' => $this->req['bonus'][$i],
                        'id_satuan' => $this->req['id_satuan'][$i],
                        'qty_lg' => $this->req['qty_lg'][$i],
                        'qty' => $qty,

                        'merek' => $this->req['merek'][$i],
                        'barang_sesuai' => $this->req['barang_sesuai'][$i],
                        'keterangan' => $this->req['kets'][$i],
                        'tgl_exp'   => $this->req['tgl_exp'][$i],
                        'sisa' => $qty,
                    ]);

                    // Perubahan Status Item PO
                    if($this->req['bonus'][$i] == 0){
                        $itempo = data_po_item::find($this->req['id_po_item'][$i]);
                        if($this->req['qty_lg'][$i] >= $this->req['req_qty'][$i]){
                            $itempo->status = 3;
                            $itempo->qty = ($itempo->qty - $this->req['qty_lg'][$i]);
                            $itempo->save();

                            // Perubahan status PRQ
                            $count = data_prq_item::where('id_prq', $itempo->id_prq)->where('status', 1)->count();
                            if($count == 0){
                                data_prq::find($itempo->id_prq)->update([
                                    'status' => 3
                                ]);
                            }

                        }else{
                            $statuspo[] = 1;
                            $itempo->status = 2;
                            $itempo->qty = ($itempo->qty - $this->req['qty_lg'][$i]);
                            $itempo->save();
                        }
                    }

                    // Log Stok
                    data_log_barang::create([
                        'id_barang' => $this->req['id_barang'][$i],
                        'qty' => $qty,
                        'keterangan' => 'Good Receive',
                        'id_gudang' => 0,
                        'kondisi' => 1,
                        'tipe' => 2,
                        'id_parent' => $gr->id_spbm,
                        'id_karyawan' => \Me::data()->id_karyawan
                    ]);

                    // Udate Stok ke data barang
                    $b = data_barang::find($this->req['id_barang'][$i]);
                    $b->in = $b->in + $qty;
                    $b->save();

                }


            }
            // Merubah Status PO
            $po = data_po::find($this->req['id_po']);
            if(count($statuspo) > 0 ){
                $po->update([
                    'status' => 2
                ]);
            }else{
                $po->update([
                    'status' => 3
                ]);
            }

            $format = 'GR-';
            $gr->no_spbm = $format . \Format::code($gr->id_spbm);
            $gr->save();

            \Loguser::create('Membuat Permohonan Barang Masuk dengan No. ' . $gr->no_spbm);
            \DB::commit();

            return [
                'label' => 'seccess',
                'err' => 'PO berhasil diproses! dengan No. ' . $gr->no_spbm
            ];

        }catch(\Exception $e){
            \DB::rollback();

            return [
                'label' => 'danger',
                'err' => $e->getMessage()
            ];
        }

    }
}
