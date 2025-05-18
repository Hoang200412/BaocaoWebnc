<?php

namespace App\Http\Controllers;
use App\Models\Banner;
use App\Models\Product;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
 


class HomeController extends Controller
{
    public function index() {
        $banners = Banner::query()->get();
        $products_best = Product::where('quantity', '<', 10)->get();
        $product_sell = Product::latest()->take(5)->get();
        $customer = User::where('id', 2)->first();
        $settings = Setting::all();

        $hightrate_1 = Product::latest()->first();
        $hightrate_2 = Product::oldest()->first();
        return view('project_1.customer.home', compact('banners', 'products_best', 'product_sell','customer','settings' , 'hightrate_1', 'hightrate_2'));
    }

    public function show(Product $product) {
    
        return view('project_1.customer.product.index', compact('product'));
    }

    public function editInformation(){
        return(view('project_1.customer.information.edit'));
    }


    public function updateInformation(Request $request){
        // Lấy thông tin từ form
        $address = $request->address;
        $email = $request->email;
        $username = $request->username;
        $user = Auth::user();
    
        // Kiểm tra người dùng đã đăng nhập chưa
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để cập nhật thông tin.');
        }
        // dd($request->all());
        // Kiểm tra và xử lý avatar
        $avatarPath = $user->avatar;
         // Giữ nguyên ảnh cũ nếu không có ảnh mới
        if ($request->file('avatar')) {
            // Xóa ảnh cũ nếu có
            
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
    
            // Lưu ảnh mới vào thư mục 'public/customer'
            $avatarPath = $request->file('avatar')->store('customer', 'public');
        }
    
        // Cập nhật thông tin người dùng
        $user->update([
            'address' => $address,
            'email' => $email,
            'username' => $username,
            'avatar' => $avatarPath, // Lưu đường dẫn avatar mới
        ]);
    
        return redirect()->route('home')->with('success', 'Cập nhật thành công!');
    }

    public function editPassword(){
        return(view('project_1.customer.information.changePassword'));
    }

    public function updatePassword(Request $request){
  
        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'min:4', 'confirmed'],
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng']);
        }

        auth()->user()->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->back()->with('success', 'Đổi mật khẩu thành công');
    }
        
}
