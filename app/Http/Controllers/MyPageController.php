<?php
namespace App\Http\Controllers;
class MyPageController extends Controller {
    public function sell(){ return response('mypage sell',200); }
    public function buy(){ return response('mypage buy',200); }
    public function likes(){ return response('mypage likes',200); }
    public function editProfile(){ return response('mypage profile edit',200); }
    public function updateProfile(){ return back(); }
}
