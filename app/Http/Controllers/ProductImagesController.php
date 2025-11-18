<?php

namespace App\Http\Controllers;

use App\Models\ProductImage;
use Illuminate\Http\Request;

class ProductImagesController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'images.*'   => 'required|image|max:2048|mimes:jpeg,png,jpg,gif,svg',
        ]);

        foreach ($request->file('images', []) as $image) {
            $path = $image->store('product_images', 'public');
            ProductImage::create([
                'product_id' => $request->input('product_id'),
                'image_path' => $path,
            ]);
        }

        return redirect()->back()->with('success', 'Images uploaded successfully.');
    }
}
