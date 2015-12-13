<?php

namespace App\Jobs\Akutansi\Faktur;

use App\Models\data_jurnal;
use App\Models\data_faktur;
use App\Models\ref_coa_ledger;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

class SaveJurnalJob extends Job implements SelfHandling {

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
    public function handle(){

        try{

            \DB::begintransaction();
            /* KREDIT */
            data_jurnal::create([
                'id_faktur' => $this->req['id_faktur'],
                'id_coa_ledger' => $this->req['id_coa_ledger'],
                'tanggal' => date('Y-m-d', strtotime($this->req['tanggal'])),
                'deskripsi' => $this->req['deskripsi'],
                'id_payment_methode' => $this->req['id_payment_methode'],
                'tipe' => 2,
                'total' => $this->req['total'],
            ]);

            /* DEBET */
            data_jurnal::create([
                'id_faktur' => $this->req['id_faktur'],
                'id_coa_ledger' => $this->req['perkiraan'],
                'tanggal' => date('Y-m-d', strtotime($this->req['tanggal'])),
                'deskripsi' => $this->req['deskripsi'],
                'id_payment_methode' => $this->req['id_payment_methode'],
                'tipe' => 1,
                'total' => $this->req['total'],
            ]);

            $faktur = data_faktur::find($this->req['id_faktur']);

            if($this->req['total'] == $this->req['total_old'])
                $faktur->status = 2;
            else
                $faktur->status = 1;

            $faktur->amount_due = $faktur->amount_due + $this->req['total'];
            $faktur->save();

            /* KREDIT */
            $coa = ref_coa_ledger::find($this->req['id_coa_ledger']);
            $coa->balance = $coa->balance + $this->req['total'];
            $coa->save();

            /* DEBET */
            $kira = ref_coa_ledger::find($this->req['perkiraan']);
            $kira->balance = $kira->balance + $this->req['total'];
            $kira->save();

            \DB::commit();
            
            return [
                'label' => 'success',
                'err' => 'Payment berhasil dilakukan'
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
