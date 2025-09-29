<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
class SellController extends Controller {
    public function create(){ return response('sell create',200); }
    public function store(Request $r){ return back(); }
}
