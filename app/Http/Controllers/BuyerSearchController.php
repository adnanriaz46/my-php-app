<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class BuyerSearchController extends Controller
{
    public function index(Request $request)
    {
        // You can pass data here later
        return Inertia::render('buyers/Search');
    }
}
