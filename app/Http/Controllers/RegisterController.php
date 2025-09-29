<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
class RegisterController extends Controller {
    public function showForm(){ return response('register form',200); }
    public function submit(Request $r){ return redirect()->route('register.thanks'); }
}
