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
        return $this->belongsTo(Karte::class);
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

    // gets groups with most found places
    public function getTopGroups($limit) {

        return DB::table('speles')
            ->where('speles.id', $this->id)
            ->leftJoin('grupas', 'speles.id', '=', 'grupas.spele_id')
            ->leftJoin('lietotajsgrupa', 'grupas.id', '=', 'lietotajsgrupa.grupa_id')
            ->leftJoin('users', 'lietotajsgrupa.user_id', '=', 'users.id')
            ->where('lietotajsgrupa.uzaicinats', 0)
            ->where('lietotajsgrupa.apstiprinats', 1)
            ->leftJoin('atrastavieta', 'grupas.id', '=', 'atrastavieta.grupa_id')
            ->leftJoin('vietas', 'atrastavieta.vieta_id', '=', 'vietas.id')

            ->select('users.name', 'users.profile_picture',
                DB::raw('sum(vietas.sarezgitiba) as punkti'))

            ->groupBy('users.name', 'users.profile_picture')
            ->orderBy('punkti', 'desc')
            ->orderBy('users.name', 'asc')
            ->limit($limit)
            ->get();

    }

    // same as getTopGroups, but get all groups, but only the ones
    // which got least 1 point
    public function getGameResults() {

        return DB::table('speles')
            ->where('speles.id', $this->id)
            ->leftJoin('grupas', 'speles.id', '=', 'grupas.spele_id')
            ->leftJoin('lietotajsgrupa', 'grupas.id', '=', 'lietotajsgrupa.grupa_id')
            ->leftJoin('users', 'lietotajsgrupa.user_id', '=', 'users.id')
            // select only groups which had at least 1 active member in the end:
            // ->whereExists(function ($query) {
            //     $query->select(DB::raw(1))
            //         ->from('lietotajsgrupa as lg')
            //         ->whereRaw('lg.grupa_id = grupas.id')
            //         ->where('lg.active', 1);
            // })
            ->where('lietotajsgrupa.uzaicinats', 0)
            ->where('lietotajsgrupa.apstiprinats', 1)
            ->leftJoin('atrastavieta', 'grupas.id', '=', 'atrastavieta.grupa_id')
            ->leftJoin('vietas', 'atrastavieta.vieta_id', '=', 'vietas.id')

            ->select('users.name', 'users.profile_picture',
                DB::raw('sum(vietas.sarezgitiba) as punkti'))
            
            ->havingRaw('SUM(vietas.sarezgitiba) > 0')
            ->groupBy('users.name', 'users.profile_picture')
            ->orderBy('punkti', 'desc')
            ->orderBy('users.name', 'asc')
            ->get();
    }

    // get all game chat messages with user info (name, profile picture)
    // in reverse order
    public function getChatMessagesReverse() {
        return DB::table('speles')
            ->where('speles.id', $this->id)
            ->join('sazina', 'speles.id', '=', 'sazina.spele_id')
            ->leftJoin('users', 'sazina.user_id', '=', 'users.id')
            ->select('sazina.text', 'users.name', 'users.profile_picture')
            ->orderBy('sazina.created_at', 'desc')
            ->get();
    }

}
