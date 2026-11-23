<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductCatalogController extends Controller
{

public function index(Request $request): View
{
    $categories = Category::orderBy('name')->get();

    $products = Product::with('category')
        ->active()
        ->when($request->filled('category'), function ($query) use ($request) {
            $slug = $request->string('category')->toString();
            $query->whereHas('category', fn ($q) => $q->where('slug', $slug));
        })
        ->when($request->filled('q'), function ($query) use ($request) {
            $keyword = $request->string('q')->toString();
            $query->where(fn ($q) => $q
                ->where('name', 'like', "%{$keyword}%")
                ->orWhere('description', 'like', "%{$keyword}%"));
        })
        ->when($request->filled('min_price'), function ($query) use ($request) {
            $query->where('price', '>=', $request->input('min_price'));
        })
        ->when($request->filled('max_price'), function ($query) use ($request) {
            $query->where('price', '<=', $request->input('max_price'));
        })
        ->when($request->boolean('in_stock'), function ($query) {
            $query->where('stock', '>', 0);
        })
        ->when($request->input('sort') === 'price_asc', function ($query) {
            $query->orderBy('price', 'asc');
        })
        ->when($request->input('sort') === 'price_desc', function ($query) {
            $query->orderBy('price', 'desc');
        })
        ->when($request->input('sort') === 'name_asc', function ($query) {
            $query->orderBy('name', 'asc');
        })
        ->when(!in_array($request->input('sort'), [
            'price_asc',
            'price_desc',
            'name_asc'
        ]), function ($query) {
            $query->latest();
        })
        ->paginate(12)
        ->withQueryString();

    return view('shop.index', compact('categories', 'products'));
}

    public function show(Product $product): View
    {
        abort_unless($product->is_active, 404);
        $product->load('category');

        $related = Product::with('category')
            ->active()
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->id)
            ->latest()
            ->take(4)
            ->get();

        return view('shop.show', compact('product', 'related'));
    }
}