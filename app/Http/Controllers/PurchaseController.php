<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
class PurchaseController extends Controller {
    public function create(){ return response('purchase create',200); }
    public function updatePayment(Request $r){ return back(); }
    public function store(Request $r){ return back(); }
}
