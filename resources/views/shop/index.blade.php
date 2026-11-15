@extends('layouts.app')
@section('title', 'Katalog Produk')
@section('content')
<div class="hero">
    <div>
        <span class="badge">Belanja Mudah</span>
        <h1>Temukan produk kebutuhan harian dan elektronik pilihan.</h1>
        <p class="muted">Pilih produk, cek stok real-time, lalu lanjutkan ke keranjang dan checkout.</p>
    </div>
    <form method="GET" action="{{ route('shop.index') }}">
        <label>Cari Produk
            <input class="input" name="q" value="{{ request('q') }}" placeholder="Ketik nama produk...">
        </label>
        <label>Kategori
            <select name="category">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </label>
        <button class="btn" type="submit">Terapkan Filter</button>
    </form>
</div>

<div class="product-grid">
    @forelse($products as $product)
        <article class="card product-card">
            <a href="{{ route('shop.show', $product->slug) }}">
                <img src="{{ $product->image ? asset('storage/'.$product->image) : 'https://placehold.co/600x450?text=Produk' }}" alt="{{ $product->name }}">
            </a>
            <span class="muted">{{ $product->category->name }}</span>
            <h3><a href="{{ route('shop.show', $product->slug) }}" style="color:inherit;text-decoration:none">{{ $product->name }}</a></h3>
            <div class="price">Rp{{ number_format($product->price, 0, ',', '.') }}</div>
            <p class="muted">Stok: {{ $product->stock }}</p>
            @if(Route::has('cart.store'))
                <form method="POST" action="{{ route('cart.store') }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button class="btn" type="submit" @disabled($product->stock < 1)>
                        {{ $product->stock > 0 ? '+ Keranjang' : 'Stok Habis' }}
                    </button>
                </form>
            @else
                <a class="btn" href="{{ route('shop.show', $product->slug) }}">Lihat Detail</a>
            @endif
        </article>
    @empty
        <div class="card">Belum ada produk aktif yang sesuai filter.</div>
    @endforelse
</div>

<div style="margin-top:20px">{{ $products->links() }}</div>
@endsection