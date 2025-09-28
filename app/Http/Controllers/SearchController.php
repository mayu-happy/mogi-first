<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');

        return view('search.index', compact('keyword'));
    }
}
