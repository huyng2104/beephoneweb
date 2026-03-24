<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        if (view()->exists('client.home.index')) {
            return view('client.home.index');
        }
        return response('Home page not implemented yet', 200);
    }
}
