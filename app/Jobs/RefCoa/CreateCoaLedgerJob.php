<?php

namespace App\Jobs\RefCoa;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

use App\Models\ref_coa_ledger;

class CreateCoaLedgerJob extends Job implements SelfHandling
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $req;

    public function __construct(array $req){
        $this->req = $req;
    }
    
    public function handle(){
        return ref_coa_ledger::create([
            'grup_coa' => $this->req['idparent'],
            'nm_coa_ledger' => $this->req['nm_coa_ledger'],
			'no_coa_ledger' => $this->req['no_coa_ledger'],
            'status_balance' => $this->req['status_balance'],
            'balance' => $this->req['balance'],
            'keterangan' => $this->req['keterangan'],
        ]);
    }
}
