<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LietotajsGrupa extends Model
{
    use HasFactory;

    protected $table = 'lietotajsgrupa';
    protected $fillable = [
        'user_id',
        'grupa_id',
        'uzaicinats',
        'apstiprinats',
        'active'
    ];
    protected $primaryKey = ['grupa_id', 'user_id'];
    public $incrementing = false;

}
