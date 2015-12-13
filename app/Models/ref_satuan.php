<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ref_satuan extends Model{

    protected $table = 'ref_satuan';
	protected $primaryKey = 'id_satuan';
	protected $fillable = [
		'nm_satuan',
		'satuan'
	];
}
