@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-3">マイリスト</h2>

    @forelse($items as $item)
    <div class="card mb-2 p-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <strong>{{ $item->name }}</strong>
                @if($item->purchases->isNotEmpty())
                <span class="badge bg-secondary ms-2">SOLD</span>
                @endif
                {{-- いいね済み表示はそのまま --}}
                <span class="badge bg-info ms-2">いいね済み</span>
            </div>
            <a href="{{ route('items.show', $item) }}" class="btn btn-sm btn-outline-primary">詳細</a>
        </div>
    </div> @empty
    <p>マイリストは空です。</p>
    @endforelse

    @if(!empty($keyword))
    <p class="text-muted">検索中: {{ $keyword }}</p>
    @endif

    <input type="search" name="keyword" value="{{ old('keyword', $keyword) }}">

    <div class="mt-3">
        {{ $items->links() }}
    </div>
</div>
@endsection