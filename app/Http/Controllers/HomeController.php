<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * GET home page
     */
    public function index() {
        /*$this->initiateInteractiveMap();*/
        return view('index');
    }
}
