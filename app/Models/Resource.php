<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $table = 'resources';

    protected $fillable = [
        'code',
        'name',
        'qty',
        'unit',
        'brand',
        'desc',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    const CREATED_AT = 'created_at';
    
    const UPDATED_AT = 'updated_at';
}
