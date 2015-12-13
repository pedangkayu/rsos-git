<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_retur_item extends Model {

    protected $table 		= 'data_retur_item';
	protected $primaryKey 	= 'id_retur_item';
	protected $fillable 	= [
		'id_retur',
		'id_barang',
		'id_satuan',
		'status',
		'qty',
		'qty_lg'
	];
}
