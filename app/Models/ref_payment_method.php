<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ref_payment_method extends Model {

    protected $table = 'ref_payment_method';
	protected $primaryKey = 'id_payment_method';
	protected $fillable = [
		'payment_method',
		'status'
	];
}
