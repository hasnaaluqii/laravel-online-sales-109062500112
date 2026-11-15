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
                $query->where(fn ($q) => $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%"));
            })
            ->latest()
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