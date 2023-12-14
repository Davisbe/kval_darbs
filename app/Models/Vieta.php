<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Vieta extends Model
{
    use HasFactory;
    protected $table = 'vietas';
    protected $fillable = [
        'name',
        'garums',
        'platums',
        'pielaujama_kluda',
        'sarezgitiba',
        'picture'
    ];

    public function grupa():BelongsToMany
    {
        return $this->belongsToMany(Grupa::class, 'atrastavieta', 'vieta_id', 'grupa_id')->withTimestamps();
    }

    public function spelevieta():BelongsToMany
    {
        return $this->belongsToMany(SpeleVieta::class, 'spelevieta', 'vieta_id', 'spele_id')->withTimestamps();
    }

}
