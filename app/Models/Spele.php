<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Support\Facades\DB;

class Spele extends Model
{
    use HasFactory;
    protected $table = 'speles';
    protected $fillable = [
        'karte_id',
        'name',
        'description',
        'picture',
        'start_time',
        'end_time',
    ];

    public function karte():BelongsTo
    {
        return $this->belongsTo(Karte::class)->withTimestamps();
    }

    public function spele():BelongsToMany {
        return $this->belongsToMany(Vieta::class, 'spelevieta', 'spele_id', 'vieta_id')->withTimestamps();
    }

    public function grupa():HasMany {
        return $this->hasMany(Grupa::class);
    }

    public function sazina():HasMany {
        return $this->hasMany(Sazina::class);
    }

    // get all games that are not ended, also figure out how many players have joined to each game (i.e. how many players are connected to the groups which are conencted to the game)
    public static function getAvailableGames() {
        
    }



}
