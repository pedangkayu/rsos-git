<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ref_ket_retur extends Model {

    protected $table = 'ref_ket_retur';
    protected $primaryKey = 'id_ket_retur';
	protected $fillable = [
		'keterangan',
		'tipe', /* 1:return Internal|2: return Eksternal */
		'status'
	];
}
