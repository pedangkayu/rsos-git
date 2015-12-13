<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ref_coa_ledger extends Model
{
    protected $table 		= 'ref_coa_ledger';
	protected $primaryKey 	= 'id_coa_ledger';
	protected $fillable 	= [
		'grup_coa',
		'no_coa_ledger',
		'nm_coa_ledger',
		'status_balance',
		'balance',
		'keterangan',
	];
}
