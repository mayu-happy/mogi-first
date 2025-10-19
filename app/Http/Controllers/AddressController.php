<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
class AddressController extends Controller {
    public function create(){ return response('address create',200); }
    public function edit(){ return response('address edit',200); }
    public function update(Request $r){ return back(); }
}
