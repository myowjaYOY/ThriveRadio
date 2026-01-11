<?php

namespace App\Http\Controllers\Api;

use App\Models\City;
use App\Models\Token;
use App\Models\Slider;
use App\Models\Category;
use App\Models\RadioStation;
use Illuminate\Http\Request;
use App\Models\RadioStationReport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    public function registerToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }
        try{

            $registerToken = Token::where('token',$request->token)->first();

            if($registerToken == null){

                $token = new Token();
                $token->token = $request->token;
                $token->save();

                $response = array(
                    'error' => false,
                    'message' => 'Token Registered Sucessfully',
                    'data' => $token
                );
            }
            else{
                $response = array(
                    'error' => false,
                    'message' => 'Token Already Registered',
                    'data' => $request->token
                );
            }
        }catch (\Throwable $e) {
            $response = array(
                'error' => true,
                'message' => $e
            );
        }
        return response()->json($response);
    }

    public function getCity(Request $request)
    {
        try {
            $offset = $request->offset;
            $limit = $request->limit;
            $search = $request->search;
            $sort = 'name';
            $order = 'ASC';

            $city = City::offset($offset)->limit($limit)->orderBy($sort, $order)->get();

            if($search)
            {
                $city = City::where(function($query) use ($search){
                    $query->where('name','LIKE', "%$search%");
                })->orderBy($sort, $order)->offset($offset)->limit($limit)->get();
            }

            $response = array(
                'error' => false,
                'message' => 'City Fetched Sucessfully',
                'data' => $city
            );
        } catch (\Throwable $e) {
            $response = array(
                'error' => true,
                'message' => $e
            );
        }
        return response()->json($response);
    }

    public function getCategory(Request $request)
    {
        try {
            $offset = $request->offset;
            $limit = $request->limit;
            $search = $request->search;
            $sort = 'id';
            $order = 'DESC';

            $category = Category::offset($offset)->limit($limit)->orderBy($sort, $order)->get();

            if($search)
            {
                $category = Category::where(function($query) use ($search){
                    $query->where('name','LIKE', "%$search%");
                })->offset($offset)->limit($limit)->get();
            }

            $response = array(
                'error' => false,
                'message' => 'Category Fetched Sucessfully',
                'data' => $category
            );
        } catch (\Throwable $e) {
            $response = array(
                'error' => true,
                'message' => $e
            );
        }
        return response()->json($response);
    }

    public function getSlider()
    {
        try {
            $slider = Slider::with('city:id,name','category:id,name','radioStation:id,name')->orderby('sequence')->get();

            $response = array(
                'error' => false,
                'message' => 'Slider Fetched Sucessfully',
                'data' => $slider
            );
        } catch (\Throwable $e) {
            $response = array(
                'error' => true,
                'message' => $e
            );
        }
        return response()->json($response);
    }
    public function getRadioStation(Request $request)
    {
        try {
            $offset = $request->offset;
            $limit = $request->limit;
            $search = $request->search;
            $sort = 'id';
            $order = 'DESC';

            $radiostation = RadioStation::with('city:id,name','category:id,name')
                ->when($search , function ($query) use ($search){
                    $query->where('name', 'LIKE', "%$search%")
                    ->orWhereHas('city', function($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%");
                    })
                    ->orWhereHas('category', function($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%");
                    });
                })->when(request('city_id') != null, function($query){
                    $city_id = request('city_id');
                    $query->where('city_id', $city_id);
                })->when(request('category_id') != null, function($query){
                    $category_id = request('category_id');
                    $query->where('category_id', $category_id);
                })->when(request('radio_station_id') != null, function($query){
                    $radiostation_id = request('radio_station_id');
                    $query->where('id', $radiostation_id);
                })
                ->offset($offset)->limit($limit)->orderBy($sort, $order)->get();

            $response = array(
                'error' => false,
                'message' => 'Radio Station Fetched Sucessfully',
                'data' => $radiostation
            );
        } catch (\Throwable $e) {
            $response = array(
                'error' => true,
                'message' => $e
            );
        }
        return response()->json($response);
    }

    public function getSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:privacy_policy,about_us,terms_condition,app_setting',
        ]);

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }
        try {
            $settings = getSettings();
            if($request->type == 'app_setting')
            {
                $data['app_name'] = $settings['app_name'] ?? "";
                $data['timezone'] = $settings['timezone'] ?? "";
                $data['city_mode'] = $settings['city_mode'] ?? "";
                $data['primary_color'] = $settings['primarycolor'] ?? "";
                $data['background_color'] = $settings['backgroundcolor'] ?? "";
                $data['dark_primary_color'] = $settings['darkprimarycolor'] ?? "";
                $data['dark_background_color'] = $settings['darkbackgroundcolor'] ?? "";

                if (isset($settings['banner_ad_status']) && $settings['banner_ad_status'] == 1) {
                    $data['banner_ad_id_android'] = $settings['banner_ad_id_android'] ?? "";
                    $data['banner_ad_id_ios'] = $settings['banner_ad_id_ios'] ?? "";
                }

                if (isset($settings['interstitial_ad_status']) && $settings['interstitial_ad_status'] == 1) {
                    $data['interstitial_ad_id_android'] = $settings['interstitial_ad_id_android'] ?? "";
                    $data['interstitial_ad_id_ios'] = $settings['interstitial_ad_id_ios'] ?? "";
                }

                $data['app_link'] = $settings['app_link'] ?? "";
                $data['ios_app_link'] = $settings['ios_app_link'] ?? "";
                $data['app_version'] = $settings['app_version'] ?? "";
                $data['ios_app_version'] = $settings['ios_app_version'] ?? "";
                $data['force_app_update'] = $settings['force_app_update'] ?? "";
                $data['app_maintenance'] = $settings['app_maintenance'] ?? "";

            }else {
                $data = $settings[$request->type] ?? "";
            }

            $response = array(
                'error' => false,
                'message' => 'Settings Fetched Sucessfully',
                'data' => $data
            );
        }  catch (\Throwable $e) {
            $response = array(
                'error' => true,
                'message' => $e
            );
        }
        return response()->json($response);
    }

    public function reportRadioStation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'radio_station_id' => 'required|integer',
            'message' => 'required|string',
            'date' => 'required|date'
        ]);

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }

        try {
            $report = new RadioStationReport();
            $report->radio_station_id = $request->radio_station_id;
            $report->message = $request->message;
            $report->date = $request->date;
            $report->save();

            $response = array(
                'error' => false,
                'message' => 'Reported Radio Station Successfully',
                'data' => $report
            );
        }  catch (\Throwable $e) {
            $response = array(
                'error' => true,
                'message' => $e
            );
        }
        return response()->json($response);
    }
}
