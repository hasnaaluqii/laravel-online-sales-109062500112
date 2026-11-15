@extends('layouts.app')
@section('title', $product->name)
@section('content')
<div class="grid-2">
    <div class="card">
        <img style="width:100%;border-radius:12px" src="{{ $product->image ? asset('storage/'.$product->image) : 'https://placehold.co/700x520?text=Produk' }}" alt="{{ $product->name }}">
    </div>
    <div class="card">
        <span class="badge">{{ $product->category->name }}</span>
        <h1>{{ $product->name }}</h1>
        <p class="muted">SKU: {{ $product->sku }}</p>
        <p class="price">Rp{{ number_format($product->price, 0, ',', '.') }}</p>
        <p>{{ $product->description ?: 'Belum ada deskripsi produk.' }}</p>
        <p><strong>Stok tersedia:</strong> {{ $product->stock }}</p>
        @if(Route::has('cart.store'))
            <form method="POST" action="{{ route('cart.store') }}">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <label>Jumlah Beli
                    <input class="input" type="number" name="quantity" value="1" min="1" max="{{ max(1, $product->stock) }}" style="max-width:140px">
                </label>
                <button class="btn" type="submit" @disabled($product->stock < 1)>
                    {{ $product->stock > 0 ? 'Masukkan ke Keranjang' : 'Stok Habis' }}
                </button>
            </form>
        @endif
    </div>
</div>
@endsection