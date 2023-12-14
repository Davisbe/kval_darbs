<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    use HasFactory;

    protected $table = 'friends';
    protected $primaryKey = ['user1_id', 'user2_id'];
    public $incrementing = false;
    protected $fillable = [
        'user1_id',
        'user2_id',
        'denied'
    ];

    // No relationships are needed to be defined with the User
    // model, as this technically is a pivot table

}
