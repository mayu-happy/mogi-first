@extends('layouts.app')
@section('title', 'プロフィール編集')

@section('content')
<div class="mypage-container">

    {{-- 任意。編集画面でもサマリー表示したいなら --}}
    @include('mypage.profile._summary', [
    'user' => $user,
    'sells' => $sells ?? collect(),
    'buys' => $buys ?? collect(),
    ])

    <form method="POST" action="{{ route('mypage.profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div>
            <label>ユーザー名</label>
            <input name="name" value="{{ old('name', $user->name) }}">
        </div>

        <div>
            <label>郵便番号</label>
            <input name="postal_code" value="{{ old('postal_code', $user->postal_code) }}">
        </div>

        <div>
            <label>住所</label>
            <input name="address" value="{{ old('address', $user->address) }}">
        </div>

        <div>
            <label>建物名</label>
            <input name="building" value="{{ old('building', $user->building) }}">
        </div>

        <div>
            <label>プロフィール画像</label>
            <input type="file" name="image">
            @if(optional($user->profile)->image)
            <div>現画像: {{ $user->profile->image }}</div>
            @endif
        </div>

        <button type="submit">保存</button>
    </form>
</div>
@endsection