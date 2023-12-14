<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpeleVieta extends Model
{
    use HasFactory;

    protected $table = 'spelevieta';
    protected $primaryKey = ['spele_id', 'vieta_id'];
    public $incrementing = false;

}
