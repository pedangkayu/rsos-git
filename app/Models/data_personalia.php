<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_personalia extends Model
{
    protected $table = 'data_personalia';
	protected $fillable = [
		'id_karyawan',
		'id_status',
		'surat_keputusan',
		'datetime_in',
		'datetime_out',
		'keterangan'
	];
}
