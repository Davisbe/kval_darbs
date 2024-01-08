<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Karte extends Model
{
    use HasFactory;

    protected $table = 'kartes';
    protected $fillable = [
        'name',
        'viduspunkts_garums',
        'viduspunkts_platums',
        'zoom',
    ];

    public function kartesobjekts():HasMany
    {
        return $this->hasMany(KartesObjekts::class);
    }

    public function spele():HasMany {
        return $this->hasMany(Spele::class);
    }


}
