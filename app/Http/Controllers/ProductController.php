<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View 
    {
        $skus = Product::pluck('sku')->unique();

        $query = Product::with('productImages');

        if ($request->filled('name')) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }

        if ($request->filled('price')) {
            $query->where('price', $request->price);
        }

        if ($request->filled('sku')) {
            $query->where('sku',$request->sku);
        }

        $products = $query->latest()->paginate(5);

        return view("products.index", compact("products", "skus"));
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

        DB::beginTransaction();
        try {

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

            DB::commit();
            return redirect()->back()->with('success', 'Product saved successfully');
        
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
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
    public function update(Request $request, Product $product)
    {
        $existingImages = $request->input('existing_images', []);
        $hasExistingImages = count($existingImages) > 0;
        $hasNewImages = $request->hasFile('images');

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'status' => 'required|in:active,inactive',
            'description' => 'required|string', 
            'images' => [$hasExistingImages || $hasNewImages ? 'nullable' : 'required', 'array'], 
            'images.*' => 'image|max:5120', 
        ]);

        DB::beginTransaction();

        try {

            $product->update($request->only([
                'name','price','status','description',
            ]));

            $product->productImages()->whereNotIn('featured_image', $existingImages)->get()
            ->each(function($image){
                Storage::disk('public')->delete($image->featured_image);
                $image->delete();
            });

            if($hasNewImages){
                foreach($request->file('images') as $image) {
                    $path = $image->store('products', 'public');
                    $product->productImages()->create([
                        'featured_image' => $path
                    ]);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Product updated successfully');

        } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
    }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $existingImages = $existingImages ?? [];

        if (!$product->exists) {
            return redirect()->back()->with('error', 'Invalid product');
        }

        DB::transaction(function () use ($product, $existingImages) {
            $product->productImages()
                    ->whereNotIn('featured_image', $existingImages)
                    ->each(function ($image) {
                        Storage::disk('public')->delete($image->featured_image);
                        $image->delete();
                    });

            $product->delete();
        });

        return redirect()->back()->with('success', 'Product deleted successfully');
    }

    /**
     * update the product status
     */
    public function updateStatus(Product $product, Request $request)
    {
        $product->update([
            'status' => $request->validate([
                'status' => 'required|in:active,inactive'
            ])['status']
        ]);

        return response()->json(['status' => $product->status]);
    }

}
