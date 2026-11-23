
@extends('layouts.app')

@section('title', 'Katalog Produk')

@section('content')

<div class="hero">
    <div>
        <span class="badge">Belanja Mudah</span>
        <h1>Temukan produk kebutuhan harian dan elektronik pilihan.</h1>
        <p class="muted">
            Pilih produk, cek stok real-time, lalu lanjutkan ke keranjang dan checkout.
        </p>
    </div>

    <form method="GET" action="{{ route('shop.index') }}">

        <label>
            Cari Produk
            <input
                class="input"
                type="text"
                name="q"
                value="{{ request('q') }}"
                placeholder="Ketik nama produk..."
            >
        </label>

        <label>
            Kategori
            <select name="category">
                <option value="">Semua Kategori</option>

                @foreach($categories as $category)
                    <option
                        value="{{ $category->slug }}"
                        @selected(request('category') === $category->slug)
                    >
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </label>

        <label>
            Harga Minimum
            <input
                class="input"
                type="number"
                name="min_price"
                min="0"
                value="{{ request('min_price') }}"
                placeholder="Contoh: 50000"
            >
        </label>

        <label>
            Harga Maksimum
            <input
                class="input"
                type="number"
                name="max_price"
                min="0"
                value="{{ request('max_price') }}"
                placeholder="Contoh: 500000"
            >
        </label>

        <label>
            <input
                type="checkbox"
                name="in_stock"
                value="1"
                @checked(request('in_stock'))
            >
            Produk dengan stok tersedia saja
        </label>

        <label>
            Urutkan Produk
            <select name="sort">
                <option value="">Default</option>

                <option
                    value="price_asc"
                    @selected(request('sort') === 'price_asc')
                >
                    Harga Termurah
                </option>

                <option
                    value="price_desc"
                    @selected(request('sort') === 'price_desc')
                >
                    Harga Termahal
                </option>

                <option
                    value="name_asc"
                    @selected(request('sort') === 'name_asc')
                >
                    Nama Produk A-Z
                </option>
            </select>
        </label>

        <button class="btn" type="submit">
            Terapkan Filter
        </button>

    </form>
</div>

<div class="product-grid">

    @forelse($products as $product)

        <article class="card product-card">

            <a href="{{ route('shop.show', $product->slug) }}">
                <img
                    src="{{ $product->image ? asset('storage/'.$product->image) : 'https://placehold.co/600x450?text=Produk' }}"
                    alt="{{ $product->name }}"
                >
            </a>

            <span class="muted">
                {{ $product->category->name }}
            </span>

            <h3>
                <a
                    href="{{ route('shop.show', $product->slug) }}"
                    style="color:inherit;text-decoration:none"
                >
                    {{ $product->name }}
                </a>
            </h3>

            <div class="price">
                Rp{{ number_format($product->price, 0, ',', '.') }}
            </div>

            <p class="muted">
                Stok: {{ $product->stock }}
            </p>

            @if(Route::has('cart.store'))

                <form
                    method="POST"
                    action="{{ route('cart.store', $product) }}"
                >
                    @csrf

                    <input
                        type="hidden"
                        name="product_id"
                        value="{{ $product->id }}"
                    >

                    <input
                        type="hidden"
                        name="quantity"
                        value="1"
                    >

                    <button
                        class="btn"
                        type="submit"
                        @disabled($product->stock < 1)
                    >
                        {{ $product->stock > 0 ? '+ Keranjang' : 'Stok Habis' }}
                    </button>
                </form>

            @else

                <a
                    class="btn"
                    href="{{ route('shop.show', $product->slug) }}"
                >
                    Lihat Detail
                </a>

            @endif

        </article>

    @empty

        <div class="card">
            Belum ada produk aktif yang sesuai filter.
        </div>

    @endforelse

</div>

<div style="margin-top:20px">
    {{ $products->links() }}
</div>

@endsection
