<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Cartitem;


class CustomCategoryController extends Controller
{
    //
    public function index()
    {
        $categories = Category::all();
        $products = Product::latest()->paginate(8);
        return view('project_1.customer.category', compact('categories', 'products'));
    }

    public function listproduct($id){
            $categories= Category::all();

            $products = Product::where('category_id',$id)->paginate(6);
            return view('project_1.customer.category',compact('categories','products'));
    }
    public function addToCart($id){
      
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'code' => 404,
                'message' => 'Sản phẩm không tồn tại'
            ], 404);
        }

        $cart = session()->get('cart', []);
        $currentQuantity = 0;

        if (isset($cart[$id])) {
            $currentQuantity = (int) $cart[$id]['quantity'];
        }

        if (auth()->check()) {
            $cartItem = Cartitem::where('user_id', auth()->id())
                ->where('product_id', $id)
                ->first();

            if ($cartItem) {
                $currentQuantity = max($currentQuantity, (int) $cartItem->quantity);
            }
        }

        if ($currentQuantity >= (int) $product->quantity) {
            return response()->json([
                'code' => 422,
                'message' => 'Sản phẩm này chỉ còn ' . $product->quantity . ' trong kho.'
            ], 422);
        }

        $newQuantity = $currentQuantity + 1;

        $cart[$id] = [
            'name' => $product->name,
            'price' => $product->price,
            'id' => $id,
            'quantity' => $newQuantity,
            'image_path' => $product->image_path,
            'description' => $product->description,
        ];

        session()->put('cart',$cart);
        if (auth()->check()) {
            $userId = auth()->id();
    
            $cartItem = Cartitem::where('user_id', $userId)
                            ->where('product_id', $id)
                            ->first();
    
            if ($cartItem) {
                $cartItem->quantity = $newQuantity;
                $cartItem->save();
            } else {
                    Cartitem::create([
                    'user_id' => $userId,
                    'product_id' => $id,
                    'quantity' => $newQuantity,
                ]);
            }
        }
        return response()->json([
                'code'=>200,
                'message'=>'success'

        ],200);
    }
    
    
    
}
