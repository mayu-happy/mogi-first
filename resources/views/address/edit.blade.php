@extends('layouts.app')
@section('title','住所の変更')

@push('styles')
<style>
    .addr-wrap {
        max-width: 640px;
        margin: 24px auto 60px;
        padding: 0 16px;
    }

    .addr-title {
        font-size: 22px;
        font-weight: 700;
        text-align: center;
        margin: 24px 0;
    }

    .form-row {
        margin: 18px 0;
    }

    .form-row label {
        display: block;
        margin-bottom: 6px;
        color: #333;
        font-weight: 600;
    }

    .input {
        width: 100%;
        height: 40px;
        border: 1px solid #e5e5e5;
        border-radius: 6px;
        padding: 8px 10px;
    }

    .btn-primary {
        display: block;
        width: 100%;
        height: 44px;
        background: #ff6f6f;
        border: none;
        border-radius: 8px;
        color: #fff;
        font-weight: 700;
        letter-spacing: .05em;
        margin-top: 14px;
    }
</style>
@endpush

@section('content')
<div class="addr-wrap">
    <h1 class="addr-title">住所の変更</h1>

    <form method="POST" action="{{ route('address.update') }}">
        @csrf @method('PUT')
        <input type="hidden" name="back" value="{{ $back }}">

        <div class="form-row">
            <label>郵便番号</label>
            <input type="text" name="postal_code" class="input"
                value="{{ old('postal_code', $user->postal_code) }}" placeholder="例）1234567">
            @error('postal_code') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="form-row">
            <label>住所</label>
            <input type="text" name="address" class="input"
                value="{{ old('address', $user->address) }}">
            @error('address') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="form-row">
            <label>建物名</label>
            <input type="text" name="building" class="input"
                value="{{ old('building', $user->building) }}">
            @error('building') <div class="error">{{ $message }}</div> @enderror
        </div>

        <button class="btn-primary" type="submit">更新する</button>
    </form>
</div>
@endsection