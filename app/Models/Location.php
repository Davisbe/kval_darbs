<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Location extends Model
{
    use HasFactory;
    protected $table = 'location';
    protected $fillable = [
        'user_id',
        'garums',
        'platums',
        'precizitate'
    ];

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
