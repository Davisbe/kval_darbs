<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sazina extends Model
{
    use HasFactory;
    protected $table = 'sazina';
    protected $fillable = [
        'spele_id',
        'user_id',
        'text',
    ];

    public function spele()
    {
        return $this->belongsTo(Spele::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
