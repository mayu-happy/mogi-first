<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ItemController,
    PurchaseController,
    AddressController,
    SellController,
    ProfileController,
    CommentController,
    LikeController,
    MyPageController,
    ItemLikeController
};

// Home
Route::get('/', [ItemController::class, 'index'])->name('home');

// Public
Route::get('/items', [ItemController::class, 'index'])->name('items.index');
Route::get('/items/{item}', [ItemController::class, 'show'])
    ->whereNumber('item')
    ->name('items.show');

// ログイン済みだけ
Route::middleware('auth')->group(function () {

    // ===== コメント・いいね =====
    Route::post('/items/{item}/comments', [CommentController::class, 'store'])
        ->name('items.comments.store');
    Route::post('/items/{item}/likes/toggle', [LikeController::class, 'toggle'])
        ->name('items.likes.toggle');

    // ===== 購入フロー =====
    Route::get('/items/{item}/purchase', [PurchaseController::class, 'create'])
        ->name('purchase.create');
    Route::get('/items/{item}/purchase/payment',  [PurchaseController::class, 'editPayment'])
        ->name('purchase.payment.edit');
    Route::put('/items/{item}/purchase/payment',  [PurchaseController::class, 'updatePayment'])
        ->name('purchase.payment.update');
    Route::post('/items/{item}/purchase', [PurchaseController::class, 'store'])
        ->name('purchase.store');
    Route::get('/items/{item}/purchase/address',  [PurchaseController::class, 'editAddress'])
        ->name('purchase.address.edit');
    Route::put('/items/{item}/purchase/address',  [PurchaseController::class, 'updateAddress'])
        ->name('purchase.address.update');

    // ===== 出品 =====
    Route::get('/sell/create', [SellController::class, 'create'])->name('sell.create');
    Route::post('/sell', [SellController::class, 'store'])->name('sell.store');
    Route::post('/sell/images/upload', [SellController::class, 'uploadImages'])
        ->name('sell.images.upload');

    // ===== マイページ =====
    Route::prefix('mypage')->name('mypage.')->group(function () {
        Route::get('/', fn() => redirect()->route('mypage.profile'))->name('index');

        Route::get('/profile',       [MyPageController::class, 'profile'])->name('profile');

        Route::get('/profile/edit',  [ProfileController::class, 'edit'])->name('profile.edit'); // => mypage.profile.edit
        Route::put('/profile',       [ProfileController::class, 'update'])->name('profile.update');

        Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');

        Route::get('/sell',  fn() => redirect()->route('mypage.profile', ['tab' => 'sell']))->name('sell');
        Route::get('/buy',   fn() => redirect()->route('mypage.profile', ['tab' => 'buy']))->name('buy');
        Route::get('/likes', fn() => redirect()->route('mypage.profile', ['tab' => 'likes']))->name('likes');

        Route::get('/address', [AddressController::class, 'create'])->name('address');
    });
});
