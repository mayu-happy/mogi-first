<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\{
    ItemController,
    RegisterController,
    AuthController,
    PurchaseController,
    AddressController,
    SellController,
    ProfileController,
    CommentController,
    LikeController,
    MyPageController
};

/* Public */

Route::get('/', [ItemController::class, 'index'])->name('home');
Route::get('/items/{item}', [ItemController::class, 'show'])
    ->whereNumber('item')
    ->name('items.show');
Route::get('/items', [ItemController::class, 'index'])->name('items.index');
Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
Route::post('/register', [RegisterController::class, 'submit'])->name('register.submit');
Route::get('/thanks', fn() => view('auth.thanks'))->name('register.thanks');
Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');

/* Auth */
Route::middleware('auth')->group(function () {
    Route::post('/items/{item}/comments', [CommentController::class, 'store'])->name('items.comments.store');
    Route::post('/items/{item}/likes/toggle', [LikeController::class, 'toggle'])->name('items.likes.toggle');
    Route::get('/items/{item}/purchase', [PurchaseController::class, 'create'])->name('purchase.create');
    Route::post('/items/{item}/update-payment', [PurchaseController::class, 'updatePayment'])->name('purchase.updatePayment');
    Route::post('/purchase/{item}', [PurchaseController::class, 'store'])->name('purchase.store');
    Route::get('/profile/edit', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // マイページ
    Route::prefix('mypage')->name('mypage.')->group(function () {
        Route::get('/',     [MyPageController::class, 'sell'])->name('index');
        Route::get('/sell', [MyPageController::class, 'sell'])->name('sell');
        Route::get('/buy',  [MyPageController::class, 'buy'])->name('buy');
        Route::get('/likes', [MyPageController::class, 'likes'])->name('likes');
        Route::get('/address', [AddressController::class, 'create'])->name('address');

        // プロフィール（重複をここに一本化）
        Route::get('/profile',        [ProfileController::class, 'show'])->name('profile');
        Route::get('/profile/edit', [MyPageController::class, 'editProfile'])->name('profile.edit');
        Route::put('/profile', [MyPageController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/images', [ProfileController::class, 'updateAvatar'])->name('profile.images.update');
    });


    // 出品
    Route::get('/sell/create', [SellController::class, 'create'])->name('sell.create');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');
    Route::post('/sell', [SellController::class, 'store'])->name('sell.store');

    // ログアウト
    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    })->name('logout');

    // 住所変更
    Route::middleware('auth')->group(function () {
        Route::get('/address/edit',  [\App\Http\Controllers\AddressController::class, 'edit'])->name('address.edit');
        Route::put('/address',       [\App\Http\Controllers\AddressController::class, 'update'])->name('address.update');
    });
});
