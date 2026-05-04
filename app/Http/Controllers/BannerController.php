<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    //
    public function index() {
        $banners = Banner::all();

        return view('project_1.admin.banners.index', compact('banners'));
    }

    public function store(Request $request) {
        try {
            $request->validate([
                'image' => 'required|image',
            ]);
            DB::transaction(function () use($request){
                
                if($request->hasFile('image')) {
                    Banner::create([
                        'image_path' =>  Storage::disk('public')->put('banners', $request->file('image'))
                    ]);
                }
    
            });
    
            return redirect()->route('banners.index')->with('success', 'Thêm banner thành công');
        } catch (\Throwable $th) {
            return redirect()->route('banners.index')->with('error', $th->getMessage());
        }
    }

    public function delete(Banner $banner) {
        try {
            $imagePath = $banner->image_path;
            DB::transaction(function () use($banner) {
                $banner->delete();
            });

            Storage::disk('public')->delete($imagePath);

            return redirect()->route('banners.index')->with('success', 'Xóa thành công');
        } catch (\Throwable $th) {
            return redirect()->route('banners.index')->with('error', $th->getMessage());
        }
      
    }
}