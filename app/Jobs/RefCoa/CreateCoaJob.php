<?php

namespace App\Jobs\RefCoa;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

use App\Models\ref_coa;

class CreateCoaJob extends Job implements SelfHandling
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
        
        return ref_coa::create([
            'parent_id' => $this->req['idparent'],
            'no_coa' => $this->req['no_coa'],
            'nm_coa' => $this->req['nm_coa'],
            'type_coa' => $this->req['type_coa'],
            'keterangan' => $this->req['keterangan'],
            'status' => 1
        ]);
    }
}
