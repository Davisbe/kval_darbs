<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Game extends Controller
{
    public function index() {
        return view('game_pages/active_index');
    }
}
