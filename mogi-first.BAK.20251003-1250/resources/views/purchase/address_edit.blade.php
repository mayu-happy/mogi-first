@extends('layouts.app')
@section('title', '住所の変更')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/address_edit.css') }}">
@endpush

@section('content')
<div class="addr-wrap">
    <h1 class="addr-title">住所の変更</h1>

    <form class="addr-form" method="POST" action="{{ route('address.update') }}">
        @csrf
        @method('PUT')
        <input type="hidden" name="context" value="{{ $context }}">
        <input type="hidden" name="back" value="{{ $back }}">

        <div class="form-row">
            <label for="postal_code">郵便番号</label>
            <input id="postal_code" name="postal_code" type="text" inputmode="numeric"
                class="input"
                value="{{ old('postal_code', $addr->postal_code ?? '') }}"
                placeholder="例）1234567" maxlength="20" required>
            @error('postal_code') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="form-row">
            <label for="address">住所</label>
            <input id="address" name="address" type="text"
                class="input"
                value="{{ old('address', $addr->address ?? '') }}"
                placeholder="都道府県・市区町村・番地" maxlength="255" required>
            @error('address') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="form-row">
            <label for="building">建物名</label>
            <input id="building" name="building" type="text"
                class="input"
                value="{{ old('building', $addr->building ?? '') }}"
                placeholder="建物名・部屋番号など" maxlength="255">
            @error('building') <p class="error">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="btn-primary">更新する</button>
    </form>
</div>
@endsection