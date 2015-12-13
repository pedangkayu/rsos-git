<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ref_konversi_satuan extends Model {
	
	protected $table = 'ref_konversi_satuan';
	protected $primaryKey = 'id_konversi_satuan';
	protected $fillable = [
		'id_barang',
		'id_satuan_max',
		'id_satuan_min',
		'qty'
	];
	
}