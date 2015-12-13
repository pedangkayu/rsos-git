<?php

namespace App\Jobs\Pengadaan;

use App\Models\data_harga;
use App\Models\data_barang;
use App\Models\ref_kategori;
use App\Models\ref_klasifikasi;
use App\Models\data_barang_detail;
use App\Models\ref_konversi_satuan;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

class CreateBarangJob extends Job implements SelfHandling{

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
    public function handle(){
        
        try{

            \DB::begintransaction();

            $id_karyawan = \Me::data()->id_karyawan;

            $barang = data_barang::create([
                'nm_barang' => $this->req['nm_barang'],
                // 'kode' => $kode,
                'id_kategori' => $this->req['id_kategori'],
                'stok_awal' => $this->req['stok_awal'],
                'id_satuan' => $this->req['id_satuan'],
                'stok_minimal' => $this->req['stok_minimal'],
                'status' => 1,
                'id_karyawan' => $id_karyawan,
                'in' => $this->req['stok_awal'],
                'out' => 0,
                'keterangan' => $this->req['keterangan'],
                'tipe' => $this->req['tipe'],
                'ppn' => $this->req['ppn'],
                'harga_beli' => $this->req['harga_beli'],
                'harga_jual' => $this->req['harga_jual'],
                'id_klasifikasi' => $this->req['id_klasifikasi']
            ]);

            if(isset($this->req['labels']) && count($this->req['labels']) > 0){
                foreach ($this->req['labels'] as $i => $val) {
                    if(strlen($val) > 0 && strlen($this->req['values'][$i]) > 0)
                        data_barang_detail::create([
                            'id_barang' => $barang->id_barang,
                            'label' => ucwords($val),
                            'nm_detail' => $this->req['values'][$i]
                        ]);
                }
            }

            ref_konversi_satuan::create([
                'id_barang' => $barang->id_barang,
                'id_satuan_max' => $this->req['id_satuan'],
                'id_satuan_min' => $this->req['id_satuan'],
                'qty' => 1
            ]);

            if(isset($this->req['koversi_satuan']) && count($this->req['koversi_satuan']) > 0){
                foreach($this->req['koversi_satuan'] as $i => $id_satuan){
                    if($id_satuan > 0)
                        ref_konversi_satuan::create([
                            'id_barang' => $barang->id_barang,
                            'id_satuan_max' => $id_satuan,
                            'id_satuan_min' => $this->req['id_satuan'],
                            'qty' => $this->req['koversi_qty'][$i]
                        ]);
                }
            }

            data_harga::create([
                'id_barang' => $barang->id_barang,
                'harga' => $this->req['harga_beli'],
                'keterangan' => 'Harga Pertama',
                'id_karyawan' => $id_karyawan,
                'tipe' => 1
            ]);

            data_harga::create([
                'id_barang' => $barang->id_barang,
                'harga' => $this->req['harga_jual'],
                'keterangan' => 'Harga Pertama',
                'id_karyawan' => $id_karyawan,
                'tipe' => 2
            ]);

            // Pengkodean
            $tipe = $this->req['tipe'] == 1 ? 'O-' : 'B-';
            $kat    = ref_kategori::find($this->req['id_kategori']);
            $kats   = $kat->alias . '-';
            $urut = \Format::code($barang->id_barang);
            
            $jj = '';
            if($this->req['tipe'] == 1){
                $kls = ref_klasifikasi::find($this->req['id_klasifikasi']);
                $jj = '-' . $kls->kode;

            }
            
            $kode = $tipe . $kats . $urut . $jj;

            $barang->kode = $kode;
            $barang->save();

            \Loguser::create('Menambahkan item barang baru ke gudang Kode. ' . $kode);

            \DB::commit();

            return [
                'label' => 'success',
                'err' => $this->req['nm_barang'] . ' berhasil di simpan kedalam Database dengan Kode. ' . $kode
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
