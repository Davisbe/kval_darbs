<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KartesObjekts extends Model
{
    use HasFactory;

    protected $table = 'kartesobjekts';
    protected $fillable = [
        'karte_id',
        'geojson',
    ];

    public function karte()
    {
        return $this->belongsTo(Karte::class)->withTimestamps();
    }
}
