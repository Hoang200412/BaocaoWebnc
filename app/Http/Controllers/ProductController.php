<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Gallery;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private $tag ;

    public function __construct(Tag $tag){
        $this->tag= $tag;
    }
    public function index()
    {
        $products = Product::latest()->paginate(5);

        return view('project_1.admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
    
        return view('project_1.admin.products.create', compact('categories','tags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
     
        try {
            DB::transaction(function () use($request) {
                $dataproduct = [
                    'category_id'   => $request->category_id,
                    'name'          => $request->name,
                    'price'         => $request->price,
                    'description'   => $request->description,
                    'quantity'      => $request->quantity,
                ];
    
                if ($request->file('image_path')) {
                    // Lưu vào storage/app/public/uploads
                    $image_path = Storage::disk('public')->put('products', $request->file('image_path')) ;
                    $dataproduct['image_path'] = $image_path;
                }
    
                $product = Product::create($dataproduct);
              
    
                if($request->galleries) {
                    foreach ($request->galleries as $image) {
                        Gallery::create([
                            'product_id' => $product->id,
                            'image_path' => Storage::disk('public')->put('galleries', $image)
                        ]);
                    }
                }
                $tagIds =[];
                //insert tags for product
                if(!empty($request->tags)){
                    foreach($request->tags as $tagItem){
                    // insert to Tags
                    $tagInstance = $this->tag->firstOrCreate([
                        'name'=>$tagItem,
                    ]);
                    $tagIds[]=$tagInstance->id ;
                    
                }
                  $product->tags()->attach($tagIds);
                }
            });
    
            return redirect()->route('products.create')->with('success', 'Thêm dữ liệu thành công');
        } catch (\Throwable $th) {
            return redirect()->route('products.create')->with('error', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
        return 1;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        $tags = Tag::all();
        
        return view('project_1.admin.products.update',compact('product', 'tags', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
       
        try {
            DB::transaction(function () use($request, $product) {

                $dataproduct = [
                    'name'          => $request->name,
                    'category_id'   => $request->category_id,
                    'description'   => $request->description,
                    'price'         => $request->price,
                    'quantity'      => $request->quantity,
                ];
                
                if ($request->file('image_path')) {
                    
                    Storage::disk('public')->delete($product->image_path);
                    $dataproduct['image_path'] = Storage::disk('public')->put('products', $request->file('image_path')) ;
                  
                }
                $product->update($dataproduct);
                $tagIds = [];
                if(!empty($request->tags)){
                    
                    foreach($request->tags as $tagItem){
                    // insert to Tags
                    $tagInstance = $this->tag->firstOrCreate([
                        'name'=>$tagItem,
                       
                    ]); 
                    $tagIds[]=$tagInstance->id ;
                         
                }}

                $product->tags()->sync($tagIds);



                if ($request->hasFile('galleries')) {
        
                    foreach ($product->galleries as $oldGallery) {
                        \Storage::disk('public')->delete($oldGallery->image_path);
                        $oldGallery->delete();
                    }
                
                    
                    foreach ($request->file('galleries') as $uploadedImage) {
                        $path = $uploadedImage->store('galleries', 'public');
                        $product->galleries()->create([
                            'image_path' => $path
                        ]);
                    }
                } else {
                  
                    foreach ($product->galleries as $oldGallery) {
                        \Storage::disk('public')->delete($oldGallery->image_path);
                        $oldGallery->delete();
                    }
                }
            

            });
            return redirect()->route('products.index')->with('success', 'Cập nhật dữ liệu thành công');

        } catch (\Throwable $th) {
            return redirect()->route('products.edit', $product)->with('error', $th->getMessage());
        }


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            DB::transaction(function () use($product) {
               
                $product->tags()->detach();
        
                foreach ($product->galleries as $gallery) {
                    if (Storage::disk('public')->exists($gallery->image_path)) {
                        Storage::disk('public')->delete($gallery->image_path);
                    }
                }
                $product->galleries()->delete();   

                $product->delete();
                
    
            });
             
            if($product->image_path && Storage::disk('public')->exists($product->image_path))
            {
                Storage::disk('public')->delete($product->image_path);
            }
            return redirect()->route('products.index')->with('success', 'Xóa dữ liệu thành công');
        } catch (\Throwable $th) {
            return redirect()->route('products.index')->with('error', $th->getMessage());
        }
        
    }
}
