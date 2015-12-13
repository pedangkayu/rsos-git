<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ref_coa extends Model
{
    protected $table 		= 'ref_coa';
	protected $primaryKey 	= 'id_coa';
	protected $fillable 	= [
		'parent_id',
		'type_coa',
		'no_coa',
		'nm_coa',
		'status',
		'keterangan'
	];

	public function ledger()
	{
		return $this->hasMany('App\Models\ref_coa_ledger','grup_coa');
	}
}
