<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $fillable = [
        'user_id'
    ];

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class)->withTimestamps();
    }
}
