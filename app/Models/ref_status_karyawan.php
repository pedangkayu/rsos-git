<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ref_status_karyawan extends Model{

    protected $table = 'ref_status_karyawan';
	protected $fillable = [
		'tipe_status',
		'nm_status',
		'kode',
		'keterangan'
	];
}
