@extends('layouts.app')
@section('title','住所の変更')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/purchase-address.css') }}">
@endpush

@section('content')
<div class="addr-wrap">
    <h1 class="addr-title">住所の変更</h1>

    <form method="POST" action="{{ route('purchase.address.update', $item) }}" class="addr-form">
        @csrf
        @method('PUT')

        <div class="addr-field">
            <label for="postal_code" class="addr-label">郵便番号</label>
            <input id="postal_code" name="postal_code" type="text" class="addr-input"
                value="{{ old('postal_code', $address['postal_code'] ?? '') }}" placeholder="123-4567">
            @error('postal_code') <p class="addr-error">{{ $message }}</p> @enderror
        </div>

        <div class="addr-field">
            <label for="address" class="addr-label">住所</label>
            <input id="address" name="address" type="text" class="addr-input"
                value="{{ old('address', $address['address'] ?? '') }}">
            @error('address') <p class="addr-error">{{ $message }}</p> @enderror
        </div>

        <div class="addr-field">
            <label for="building" class="addr-label">建物名</label>
            <input id="building" name="building" type="text" class="addr-input"
                value="{{ old('building', $address['building'] ?? '') }}">
            @error('building') <p class="addr-error">{{ $message }}</p> @enderror
        </div>

        <div class="addr-submit">
            <button type="submit" class="btn-primary">更新する</button>
        </div>
    </form>
</div>
@endsection