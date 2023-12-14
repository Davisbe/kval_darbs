<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtrastaVieta extends Model
{
    use HasFactory;

    protected $table = 'atrastavieta';
    protected $primaryKey = ['grupa_id', 'vieta_id'];
    public $incrementing = false;
    protected $fillable = [
        'grupa_id',
        'vieta_id'
    ];
}
