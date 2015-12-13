<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_sph extends Model {

    protected $table 		= 'data_sph';
	protected $primaryKey 	= 'id_sph';
	protected $fillable 	= [
		'id_sph_grup',
		'id_vendor',
		'no_sph_item',
		'deadline',
		'id_pembuat',
		'id_acc',
		'diskon',
		'ppn',
		'pph',
		'adjustment',
		'status', /* 0:delete|1:proses & Eliminasi|2:selected |3:delete system */
		'keterangan'
	];
}
