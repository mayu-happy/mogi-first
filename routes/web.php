<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ItemController,
    RegisterController,
    PurchaseController,
    AddressController,
    SellController,
    ProfileController,
    CommentController,
    LikeController,
    MyPageController,
    LoginController
};

// Home
Route::get('/', [ItemController::class, 'index'])->name('home');

// Public
Route::get('/items', [ItemController::class, 'index'])->name('items.index');
Route::get('/items/{item}', [ItemController::class, 'show'])
    ->whereNumber('item')
    ->name('items.show');

// 登録完了ページ（誰でも見えるでOK）
Route::get('/thanks', fn() => view('auth.thanks'))->name('register.thanks');

// 未ログインだけ
Route::get('/login', [LoginController::class, 'show'])
    ->middleware('guest')->name('login');

Route::post('/login', [LoginController::class, 'authenticate'])
    ->middleware('guest')->name('login.attempt');

Route::get('/register', [RegisterController::class, 'showForm'])
    ->middleware('guest')->name('register');

Route::post('/register', [RegisterController::class, 'submit'])
    ->middleware('guest')->name('register.submit');

// ログイン済みだけ
Route::middleware('auth')->group(function () {
    // コメント・いいね・購入
    Route::post('/items/{item}/comments', [CommentController::class, 'store'])->name('items.comments.store');
    Route::post('/items/{item}/likes/toggle', [LikeController::class, 'toggle'])->name('items.likes.toggle');
    Route::get('/items/{item}/purchase', [PurchaseController::class, 'create'])->name('purchase.create');
    Route::post('/items/{item}/update-payment', [PurchaseController::class, 'updatePayment'])->name('purchase.updatePayment');
    Route::post('/purchase/{item}', [PurchaseController::class, 'store'])->name('purchase.store');

    // プロフィール
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');

    // マイページ
    Route::prefix('mypage')->name('mypage.')->group(function () {
        Route::get('/', [MyPageController::class, 'sell'])->name('index');
        Route::get('/sell', [MyPageController::class, 'sell'])->name('sell');
        Route::get('/buy', [MyPageController::class, 'buy'])->name('buy');
        Route::get('/likes', [MyPageController::class, 'likes'])->name('likes');
        Route::get('/address', [AddressController::class, 'create'])->name('address');

        Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
        Route::get('/profile/edit', [MyPageController::class, 'editProfile'])->name('profile.edit');
        Route::put('/profile', [MyPageController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/images', [ProfileController::class, 'updateAvatar'])->name('profile.images.update');
    });

    // 出品
    Route::get('/sell/create', [SellController::class, 'create'])->name('sell.create');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');
    Route::post('/sell', [SellController::class, 'store'])->name('sell.store');

    // ログアウト
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
