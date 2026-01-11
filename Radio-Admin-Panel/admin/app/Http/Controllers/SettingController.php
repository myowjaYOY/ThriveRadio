<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
   public function about_us()
   {
    $settings =  getSettings();
    return response(view('settings.about_us',compact('settings')));
   }

   public function privacy_policy()
   {
    $settings =  getSettings();
    return response(view('settings.privacy_policy',compact('settings')));
   }
   public function terms_condition()
   {
    $settings =  getSettings();
    return response(view('settings.terms_condition',compact('settings')));
   }
   public function system_settings()
   {
        $user = Auth::user();
        $settings =  getSettings();
        $timezones = getTimezoneList();

        return response(view('settings.system_settings',compact('user','settings','timezones')));
   }
   public function notification_settings()
   {
        $settings =  getSettings();
        return response(view('settings.notification_settings',compact('settings')));
   }
   public function update_settings(Request $request)
   {

        $validator = Validator::make($request->all(), [
            'app_name'=> 'required',
            'email' => 'required|email',
            'mobile' => 'required|number',
            'timezone' => 'required',
            'primarycolor' =>'required',
            'backgroundcolor' => 'required',
            'darkprimarycolor' =>'required',
            'darkbackgroundcolor' => 'required',
            'city_mode' =>'required',
        ]);

        $settings = ['app_name','timezone','primarycolor','backgroundcolor','city_mode','darkprimarycolor','darkbackgroundcolor'];

        try {
            $user = Auth::user();
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            if($request->hasFile('admin_image')){
                $image = $request->file('admin_image');
                $file_name = time() . '-' . $image->getClientOriginalName();
                $file_path = 'user/' . $file_name;
                $destinationPath = storage_path('app/public/user');
                $image->move($destinationPath, $file_name);
                $user->image = $file_path;
            }
            $user->save();

            foreach ($settings as $row) {
                if (Setting::where('type', $row)->exists()) {
                    // removing the double unnecessary double quotes in school name
                    if ($row == 'app_name') {
                        $data = [
                            'message' => str_replace('"', '', $request->$row)
                        ];
                    }else{
                        $data = [
                            'message' => $request->$row
                        ];
                    }
                    Setting::where('type', $row)->update($data);
                } else {
                    $setting = new Setting();
                    $setting->type = $row;
                    $setting->message = $row == 'app_name' ? str_replace('"', '', $request->$row) : $request->$row;
                    $setting->save();
                }
            }
            if ($request->hasFile('favicon')) {
                if (Setting::where('type', 'favicon')->exists()) {
                    $get_id = Setting::select('message')->where('type', 'favicon')->pluck('message')->first();
                    if (Storage::disk('public')->exists($get_id)) {
                        Storage::disk('public')->delete($get_id);
                    }
                    $data = [
                        'message' => $request->file('favicon')->store('logo', 'public')
                    ];
                    Setting::where('type', 'favicon')->update($data);
                } else {
                    $setting = new Setting();
                    $setting->type = 'favicon';
                    $setting->message = $request->file('favicon')->store('logo', 'public');
                    $setting->save();
                }
            }
            if ($request->hasFile('logo')) {
                if (Setting::where('type', 'logo')->exists()) {
                    $get_id = Setting::select('message')->where('type', 'logo')->pluck('message')->first();
                    if (Storage::disk('public')->exists($get_id)) {
                        Storage::disk('public')->delete($get_id);
                    }
                    $data = [
                        'message' => $request->file('logo')->store('logo', 'public')
                    ];
                    Setting::where('type', 'logo')->update($data);
                } else {
                    $setting = new Setting();
                    $setting->type = 'logo';
                    $setting->message = $request->file('logo')->store('logo', 'public');
                    $setting->save();
                }
            }
            if ($request->hasFile('bgimage')) {
                if (Setting::where('type', 'bgimage')->exists()) {
                    $get_id = Setting::select('message')->where('type', 'bgimage')->pluck('message')->first();
                    if (Storage::disk('public')->exists($get_id)) {
                        Storage::disk('public')->delete($get_id);
                    }
                    $data = [
                        'message' => $request->file('bgimage')->store('logo', 'public')
                    ];
                    Setting::where('type', 'bgimage')->update($data);
                } else {
                    $setting = new Setting();
                    $setting->type = 'bgimage';
                    $setting->message = $request->file('bgimage')->store('logo', 'public');
                    $setting->save();
                }
            }

            $logo = Setting::select('message')->where('type', 'logo')->pluck('message')->first();
            $favicon = Setting::select('message')->where('type', 'favicon')->pluck('message')->first();
            $app_name = Setting::select('message')->where('type', 'app_name')->pluck('message')->first();
            $timezone = Setting::select('message')->where('type', 'timezone')->pluck('message')->first();
            $bgimage = Setting::select('message')->where('type', 'bgimage')->pluck('message')->first();
            $env_update = changeEnv([
                'LOGO' => $logo,
                'FAVICON' => $favicon,
                'BGIMAGE' =>  $bgimage,
                'APP_NAME' => '"' . $app_name . '"',
                'TIMEZONE' => "'" . $timezone . "'"

            ]);
            if ($env_update) {
                $response = array(
                    'error' => false,
                    'message' => 'Settings Update Successfully',
                );
            }

        } catch (\Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e
            );
        }
        return response()->json($response);
   }

   public function update_about_us(Request $request)
   {
        try {
            if(Setting::where('type','about_us')->exists())
            {
                $data = [
                    'message' => $request->message
                ];
                Setting::where('type', 'about_us')->update($data);
            }else{
                $setting = new Setting();
                $setting->type = $request->about_us;
                $setting->message = $request->message;
                $setting->save();
            }
            $response = array(
                'error' => false,
                'message' => 'Data Update Successfully',
            );

        } catch (\Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e
            );
        }
        return response()->json($response);
   }
   public function update_privacy_policy(Request $request)
   {
        try {
            if(Setting::where('type','privacy_policy')->exists())
            {
                $data = [
                    'message' => $request->message
                ];
                Setting::where('type', 'privacy_policy')->update($data);
            }else{
                $setting = new Setting();
                $setting->type = $request->privacy_policy;
                $setting->message = $request->message;
                $setting->save();
            }
            $response = array(
                'error' => false,
                'message' => 'Data Update Successfully',
            );

        } catch (\Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e
            );
        }
        return response()->json($response);
   }

   public function update_terms_condition(Request $request)
   {
        try {
            if(Setting::where('type','terms_condition')->exists())
            {
                $data = [
                    'message' => $request->message
                ];
                Setting::where('type', 'terms_condition')->update($data);
            }else{
                $setting = new Setting();
                $setting->type = $request->terms_condition;
                $setting->message = $request->message;
                $setting->save();
            }
            $response = array(
                'error' => false,
                'message' => 'Data Update Successfully',
            );

        } catch (\Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e
            );
        }
        return response()->json($response);
   }

   public function update_notification_setting(Request $request)
    {
        $request->validate([
            'project_id' => 'required',
        ]);

        try {
            if (Setting::where('type', 'project_id')->exists()) {
                $data = [
                    'message' => $request->project_id
                ];
                Setting::where('type', 'project_id')->update($data);
            } else {
                $setting = new Setting();
                $setting->type = 'project_id';
                $setting->message = $request->project_id;
                $setting->save();
            }

            if ($request->hasFile('service_file')) {
                $serviceFile = $request->file('service_file');

                if (Setting::where('type', 'service_file')->exists()) {
                    $get_id = Setting::where('type', 'service_file')->value('message');

                    // Delete the existing file
                    if (Storage::disk('public')->exists($get_id)) {
                        Storage::disk('public')->delete($get_id);
                    }

                    // Store the new file with its original name
                    $data = [
                        'message' => $serviceFile->storeAs('firebase', $serviceFile->getClientOriginalName(), 'public')
                    ];
                    Setting::where('type', 'service_account_file')->update($data);
                } else {
                    $setting = new Setting();
                    $setting->type = 'service_file';
                    $setting->message = $serviceFile->storeAs('firebase', $serviceFile->getClientOriginalName(), 'public');
                    $setting->save();
                }
            }

            $firebase_project_id = Setting::select('message')->where('type', 'project_id')->pluck('message')->first();
            $env_update = changeEnv([
                'FIREBASE_PROJECT_ID' =>  $firebase_project_id,
            ]);
            if ($env_update) {
                $response = array(
                    'error' => false,
                    'message' => trans('Data Update Successfully'),
                );
            }
        } catch (\Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e
            );
        }

        return response()->json($response);
    }

    public function admobIndex()
    {
        $settings =  getSettings();
        return response(view('settings.admob_settings',compact('settings')));
    }

    public function updateAdmobSetting(Request $request)
    {
        $request->validate([
            'banner_ad_id_android' => 'required',
            'banner_ad_id_ios' => 'required',
            'banner_ad_status' => 'required',
            'interstitial_ad_id_android' => 'required',
            'interstitial_ad_id_ios' => 'required',
            'interstitial_ad_status' => 'required'
        ]);

        try {

            $inputs = $request->input();

            foreach ($inputs as $key => $value) {
                if (Setting::where('type', $key)->exists()) {
                    $data = [
                        'message' => $value
                    ];
                    Setting::where('type', $key)->update($data);
                } else {
                    $setting = new Setting();
                    $setting->type = $key;
                    $setting->message = $value;
                    $setting->save();
                }

            }
            $response = array(
                'error' => false,
                'message' => trans('Settings Update Successfully'),
            );

        } catch (\Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e
            );
        }

        return response()->json($response);
    }

    public function appSettingIndex()
    {
        $settings =  getSettings();
        return response(view('settings.app_settings',compact('settings')));
    }

    public function updateAppSetting(Request $request)
    {
        $request->validate([
            'app_link' => 'required',
            'ios_app_link' => 'required',
            'app_version' => 'required',
            'ios_app_version' => 'required',
            'force_app_update' => 'required',
            'app_maintenance' => 'required',
        ]);

        $settings = [
            'app_link',
            'ios_app_link',
            'app_version',
            'ios_app_version',
            'force_app_update',
            'app_maintenance',
        ];

        try {

            foreach ($settings as $row) {
                if (Setting::where('type', $row)->exists()) {

                    $data = [
                        'message' => $request->$row
                    ];
                    Setting::where('type', $row)->update($data);
                } else {
                    $setting = new Setting();
                    $setting->type = $row;
                    $setting->message = $request->$row;
                    $setting->save();
                }
            }

            $response = array(
                'error' => false,
                'message' => trans('Setting Store Successfully'),
            );
        } catch (Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e
            );
        }
        return response()->json($response);
    }
}
