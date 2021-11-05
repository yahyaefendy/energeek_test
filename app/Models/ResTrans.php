<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResTrans extends Model
{
    protected $table = 'resource';

    protected $fillable = [
        'product_id',
        'trans_id',
        'qty',
        'created_at',
        'created_by',
    ];

    public $timestamps = ["created_at"];

    const CREATED_AT = 'created_at';

    const UPDATED_AT = null;
}
