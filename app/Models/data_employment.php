<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_employment extends Model
{
    protected $table = 'data_employment';
	protected $fillable = [
		'id_recruitment',
		'nm_depan',
		'nm_belakang',
		'mobile',
		'email',
		'sex',
		'tempat_lahir',
		'tgl_lahir',
		'alamat',
		'agama',
		'pendidikan',
		'id_status',
	];
}
