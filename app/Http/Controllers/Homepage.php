<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Homepage extends Controller
{
    function index() {
        return view('homepage/index');
    }
}
