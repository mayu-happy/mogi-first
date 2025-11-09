<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse;

class LoginToHome implements LoginResponse
{
    public function toResponse($request)
    {
        return redirect()->to('/'); // ← 必要なら '/top' 等に
    }
}
