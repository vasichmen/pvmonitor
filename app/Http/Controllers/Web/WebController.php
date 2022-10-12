<?php


namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class WebController extends Controller
{
    public function mainPage()
    {
        return Inertia::render('MainPage', []);
    }
}