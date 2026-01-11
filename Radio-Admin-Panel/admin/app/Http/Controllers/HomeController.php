<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Slider;
use App\Models\Category;
use App\Models\RadioStation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function login()
    {
        if (Auth::user()) {
            return redirect('/');
        } else {
            return view('auth.login');
        }
    }

    public function index()
    {
        //dd(Auth::user());
        $city= City::count();
        $category = Category::count();
        $radioStation = RadioStation::count();
        $slider = Slider::count();
        $settings = getSettings();
        $city_mode = collect(getSettings('city_mode'))->isNotEmpty() ? getSettings('city_mode')['city_mode'] : 0 ;

        return view('dashboard',compact('city','category','radioStation','slider','settings','city_mode'));
    }

    public function change_password_index()
    {
        return view('change_password');
    }

    public function checkPassword(Request $request)
    {
        $currentPassword = $request->input('current_password');
        $user = Auth::user();

        if (Hash::check($currentPassword, $user->password)) {
            $response = array(
                'error' => false,
                'message' => "Enter New Password",
            );
        } else {
            $response = array(
                'error' => true,
                'message' => "Enter Correct Password",
            );
        }
        return response()->json($response);
    }

    public function changePassword(Request $request)
    {
        $newPassword = $request->input('new_password');
        $confirmNewPassword = $request->input('confirm_new_password');

        // Validate new password and confirm new password
        if ($newPassword !== $confirmNewPassword) {
            $response = array(
                'error' => true,
                'message' => "Password Doesn't Match",
            );
        }

        // Change the password
        $user = Auth::user();
        $user->password = Hash::make($newPassword);
        $user->save();

        $response = array(
            'error' => false,
            'message' => "Password Changed Successfully",
        );
        return response()->json($response);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->flush();
        $request->session()->regenerate();
        return redirect('/');
    }
}
