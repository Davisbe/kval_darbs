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

    public function vieta():BelongsToMany {
        return $this->belongsToMany(Vieta::class, 'spelevieta', 'spele_id', 'vieta_id')->withTimestamps();
    }

    public function grupa():HasMany {
        return $this->HasMany(Grupa::class);
    }

    public function sazina():HasMany {
        return $this->hasMany(Sazina::class);
    }

    // check if game has started
    public function hasStarted() {
        return $this->start_time < now();
    }

    // check if game has ended
    public function hasEnded() {
        return $this->end_time < now();
    }

    public function IsTimeBetweenStartAndEnd() {
        return $this->start_time < now() && $this->end_time > now();
    }



}
