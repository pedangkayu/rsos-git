<?php

namespace App\Jobs\Pembelian\SPH;

use App\Models\data_sph;
use App\Models\data_prq;
use App\Models\data_sph_grup;
use App\Models\data_sph_item;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

class CreateSPHJob extends Job implements SelfHandling {

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

            if($this->req['id'] < 1):
                $group = data_sph_grup::create([
                    'id_pembuat'    => \Me::data()->id_karyawan
                ]);
            else:
                $group = data_sph_grup::find($this->req['id']);
                \Loguser::create('Menambahkan Pengajuan pada SPH No. ' . $group->no_sph);
            endif;

            $sph = data_sph::create([
                'id_sph_grup'   => $group->id_sph_grup,
                'id_vendor'     => $this->req['vendor'],
                'deadline'      => $this->req['deadline'],
                'id_pembuat'    => \Me::data()->id_karyawan,
                'id_acc'        => 0,
                'diskon'        => $this->req['gdiskon'],
                'ppn'           => $this->req['gppn'],
                // 'pph'           => $this->req['gpph'],
                'adjustment'    => $this->req['adjustment'],
                'keterangan'    => $this->req['ket']
            ]);

            foreach($this->req['id_prq_item'] as $i => $id_prq_item){
                data_sph_item::create([
                    'id_sph'    => $sph->id_sph,
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

                data_prq::find($this->req['id_prq'][$i])->update([
                    'status' => 2
                ]);
            } 

            if($this->req['id'] < 1):
                $format = 'G-SPH/';
                $group->no_sph = $format . \Format::code($group->id_sph_grup);
                $group->save();

                \Loguser::create('Membuat Surat Pengajuan Harga dengan No. ' . $group->no_sph);
            endif;

            $fo = 'SPH/'; 
            $sph->no_sph_item = $fo . \Format::code($sph->id_sph);
            $sph->save();


            \DB::commit();

            return [
                'status'    => true,
                'err'       => $group
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
