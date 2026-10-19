<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

        if($search = $request->input('search')){
            $query->where(function ($q) use ($search){
                $q->where('name','like',"%{$search}%")
                    ->orWhere('sku','like',"%{$search}%");
            });
        }

        if($categoryId = $request->input('category')){
            $query->where('category_id', $categoryId);
        }

        if($request->input('stock_filter') === 'low'){
            $query->where('stock','<=', 5);
        }

        $products = $query->latest()->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('products.index', compact('products','categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return  view('products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'sku'         => ['required', 'string', 'max:50', 'unique:products,sku'],
            'name'        => ['required', 'string', 'max:150'],
            'cost_price'  => ['required', 'numeric', 'min:0'],
            'price'       => ['required', 'numeric', 'min:0', 'gte:cost_price'],
            'stock'       => ['required', 'integer', 'min:0'],
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active'   => ['boolean'],
        ], [
            'price.gte' => 'Harga jual tidak boleh lebih kecil dari harga modal.',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        if($request->hasFile('image')){
            $data['image'] = $request->file('image')->store('product','public');
        }

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Product brhasil di tambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load('category','movements.user');

        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();

        return view('products.edit', compact('product','categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'sku'         => ['required', 'string', 'max:50', 'unique:products,sku,' . $product->id],
            'name'        => ['required', 'string', 'max:150'],
            'cost_price'  => ['required', 'numeric', 'min:0'],
            'price'       => ['required', 'numeric', 'min:0', 'gte:cost_price'],
            'stock'       => ['required', 'integer', 'min:0'],
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active'   => ['boolean'],
        ]);
        
        $data['is_active'] = $request->boolean('is_active');

        if($request->hasFile('image')){

        //hapus gambar lama
        if($product->image && Storage::disk('public')->exists($product->image)){

            Storage::disk('public')->delete($product->image);
        }

            $data['image'] = $request->file('image')->store('product','public');
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success','Produk berhasil di ubah');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        if($product->image && Storage::disk('public')->exists($product->image)){

            Storage::disk('public')->delete($product->image);
        }
        $product->delete();

        return redirect()->route('products.index')->with('success', 'produk berhasil dihapus');
    }
}
