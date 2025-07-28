<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View 
    {
        return view("products.index");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'status' => 'required|in:active,inactive',
            'description' => 'required|string', 
            'images' => 'required|array', 
            'images.*' => 'image|max:5120', 
        ]);

        $product = Product::create([
            'name'=> $request->name,
            'slug' => Str::slug($request->name),
            'description'=> $request->description,
            'price'=> $request->price,
            'sku' => $this->generateSku(),
            'status'=> $request->status,
        ]);

        if($request->hasFile('images')){
            foreach($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $product->productImages()->create([
                    'featured_image' => $path
                ]);
            }
        }

        return redirect()->back()->with('success',  'Product saved successfuly');
    }

    /**
     * Generate SKU
     */

    private function generateSku(){
        do {
            $sku = 'SKU-'.strtoupper(Str::random(8));
        } while (Product::where('sku', $sku)->exists());

        return $sku;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
