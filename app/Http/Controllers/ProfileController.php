<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
class ProfileController extends Controller {
    public function updateAvatar(Request $r){ return back(); }
    public function edit(){ return response('profile edit',200); }
    public function update(Request $r){ return back(); }
    public function show(){ return response('profile show',200); }
}
