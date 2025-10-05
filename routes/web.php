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
    MyPageController
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
    // ★ 購入用の配送先編集（プロフィールとは別）
    Route::get('/items/{item}/purchase/address',  [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
    Route::put('/items/{item}/purchase/address',  [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');

    // ===== 出品 =====
    Route::get('/sell/create', [SellController::class, 'create'])->name('sell.create');
    Route::post('/sell', [SellController::class, 'store'])->name('sell.store');
    Route::post('/sell/images/upload', [SellController::class, 'uploadImages'])
        ->name('sell.images.upload');
    Route::get('/mypage/profile/edit', [ProfileController::class, 'edit'])
        ->name('mypage.profile.edit');

    // ===== マイページ =====
    Route::prefix('mypage')->name('mypage.')->group(function () {

        // デフォルトはプロフィール画面へ
        Route::get('/', fn() => redirect()->route('mypage.profile'))->name('index');

        // プロフィール（表示は MyPageController@profile に一本化）
        Route::get('/profile', [MyPageController::class, 'profile'])->name('profile');

        // タブはクエリで切り替え（/mypage/profile?tab=sell 等）
        Route::get('/sell',  fn() => redirect()->route('mypage.profile', ['tab' => 'sell']))->name('sell');
        Route::get('/buy',   fn() => redirect()->route('mypage.profile', ['tab' => 'buy']))->name('buy');
        Route::get('/likes', fn() => redirect()->route('mypage.profile', ['tab' => 'likes']))->name('likes');

        // プロフィール編集・更新・アイコン更新（編集系は ProfileController に一本化）
        Route::get('/profile/edit',  [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile',       [ProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/images', [ProfileController::class, 'updateAvatar'])->name('profile.images.update');
        Route::post('/profile/avatar', [\App\Http\Controllers\ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
        // 住所（必要なら）
        Route::get('/address', [AddressController::class, 'create'])->name('address');
    });
});
