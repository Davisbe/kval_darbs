<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Grupa extends Model
{
    use HasFactory;

    protected $table = 'grupas';
    protected $fillable = [
        'spele_id'
    ];

    public function vieta():BelongsToMany
    {
        return $this->belongsToMany(Grupa::class, 'atrastavieta', 'grupa_id', 'vieta_id')->withTimestamps();
    }

    public function user():BelongsToMany {
        return $this->belongsToMany(User::class, 'lietotajsgrupa', 'grupa_id', 'user_id')->withTimestamps();
    }

    public function spele():BelongsTo {
        return $this->belongsTo(Spele::class)->withTimestamps();
    }
}
