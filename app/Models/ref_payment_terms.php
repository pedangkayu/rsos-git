<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ref_payment_terms extends Model {

    protected $table = 'ref_payment_terms';
	protected $primaryKey = 'id_payment_terms';
	protected $fillable = [
		'payment_terms',
		'payment_terms_en'
	];
}
