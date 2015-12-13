<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ref_klasifikasi extends Model
{
    protected $table = 'ref_klasifikasi';
    protected $primaryKey = 'id_klasifikasi';
	protected $fillable = [
		'kode',
		'nm_klasifikasi'
	];
}
