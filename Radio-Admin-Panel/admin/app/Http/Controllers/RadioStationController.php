<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Category;
use App\Models\RadioStation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Rules\UniqueRadioStationName;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class RadioStationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cities = City::all();
        $categories = Category::all();
        $city_mode = collect(getSettings('city_mode'))->isNotEmpty() ? getSettings('city_mode')['city_mode'] : 1 ;

        return response(view('radio_station.index',compact('cities','categories','city_mode')));
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
        $validator = Validator::make($request->all(), [
            'city_id' => 'numeric|nullable|exists:cities,id',
            'category_id' => 'required|integer|exists:categories,id',
            'name' => ['required','string','max:255',new UniqueRadioStationName($request->city_id,$request->category_id)],
            'radio_url' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }
        try{
            $radioStation = new RadioStation();
            $radioStation->city_id = $request->city_id;
            $radioStation->category_id = $request->category_id;
            $radioStation->name = $request->name;
            $radioStation->radio_url = $request->radio_url;
            $radioStation->description = $request->description;

            if($request->hasFile('image'))
            {
                $image = $request->file('image');
                $file_name = time() . '-' . $image->getClientOriginalName();
                $file_path = 'radiostation/' . $file_name;
                $destinationPath = storage_path('app/public/radiostation');
                $image->move($destinationPath, $file_name);
                $radioStation->image = $file_path;
            }


            $radioStation->save();

            $response = array(
                'error' => false,
                'message' => 'Radio Station Added Sucessfully'
            );

        }catch (\Throwable $e) {

            $response = array(
                'error' => true,
                'message' => $e
            );
        }
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'DESC';

        if (isset($_GET['offset']))
        $offset = $_GET['offset'];
        if (isset($_GET['limit']))
        $limit = $_GET['limit'];

        if (isset($_GET['sort']))
        $sort = $_GET['sort'];
        if (isset($_GET['order']))
        $order = $_GET['order'];

        $sql = RadioStation::with('city','category');

        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $search = $_GET['search'];
            $sql->where('id', 'LIKE', "%$search%")->orwhere('name', 'LIKE', "%$search%")
            ->orWhereHas('city', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%");
            });
        }
        $total = $sql->count();

        $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $no=1;
        foreach ($res as $row) {
            $operate = '<a href='.route('radio-station.update',$row->id).' class="btn icon btn-primary edit editbutton" data-id=' . $row->id . '" title="Edit" data-bs-toggle="modal" data-bs-target="#editModal"><i class="fa fa-edit"></i></a>';
            $operate .= '<a href='.route('radio-station.destroy',$row->id).' class="btn icon btn-danger delete-form deletebutton" data-id=' . $row->id . '><i class="fa fa-trash"></i></a>';

            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['name'] = $row->name;
            $tempRow['city_id'] = $row->city_id ?? '';
            $tempRow['city'] = $row->city->name ?? '-';
            $tempRow['category_id'] = $row->category_id;
            $tempRow['category'] = $row->category->name;
            $tempRow['image'] = $row->image;
            $tempRow['radio_url'] = $row->radio_url;
            $tempRow['description'] = $row->description ?? '-';
            $tempRow['operate'] = $operate;
            $tempRow['created_at'] = $row->created_at;
            $tempRow['updated_at'] = $row->updated_at;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
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
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'city_id' => 'numeric|nullable|exists:cities,id',
            'category_id' => 'required|integer|exists:categories,id',
            'name' => ['required','string','max:255',new UniqueRadioStationName($request->city_id,$request->category_id,$request->id)],
            'radio_url' => 'required|string|max:255',
            'image' => 'image|mimes:jpeg,png,jpg|max:2048',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }
        try{
            $radioStation = RadioStation::find($request->id);
            $radioStation->city_id = $request->city_id;
            $radioStation->category_id = $request->category_id;
            $radioStation->name = $request->name;
            $radioStation->radio_url = $request->radio_url;
            $radioStation->description = $request->description;

            if($request->hasFile('image'))
            {
                if (Storage::disk('public')->exists($radioStation->getRawOriginal('image'))) {
                    Storage::disk('public')->delete($radioStation->getRawOriginal('image'));
                }

                $image = $request->file('image');
                $file_name = time() . '-' . $image->getClientOriginalName();
                $file_path = 'radiostation/' . $file_name;
                $destinationPath = storage_path('app/public/radiostation');
                $image->move($destinationPath, $file_name);
                $radioStation->image = $file_path;
            }


            $radioStation->save();

            $response = array(
                'error' => false,
                'message' => 'Radio Station Added Sucessfully'
            );

        }catch (\Throwable $e) {

            $response = array(
                'error' => true,
                'message' => $e
            );
        }
        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $radioStation = RadioStation::find($id);
            if (Storage::disk('public')->exists($radioStation->getRawOriginal('image'))) {
                Storage::disk('public')->delete($radioStation->getRawOriginal('image'));
            }
            $radioStation->delete();
            $response = array(
                'error' => false,
                'message' => 'Radio Station Deleted Successfully'
            );
        } catch (\Throwable $e) {
            $response = array(
                'error' => true,
                'message' => $e
            );
        }
        return response()->json($response);
    }

    public function getRadioStation($city_id, $category_id)
    {
        try {
            $radioStation = RadioStation::where('city_id',$city_id)->where('category_id',$category_id)->get();

            $response = array(
                'error' => false,
                'message' => 'Radio Station Fetched Successfully',
                'data' => $radioStation
            );

        } catch (\Throwable $e) {
            $response = array(
                'error' => true,
                'message' => $e
            );
        }
        return response()->json($response);
    }

    public function getCategory($city_id)
    {
        try {
            $category_id = RadioStation::distinct('category_id')->where('city_id',$city_id)->pluck('category_id');
            $category = Category::whereIn('id',$category_id)->get();

            $response = array(
                'error' => false,
                'message' => 'Category Fetched Successfully',
                'data' =>  $category
            );
        } catch (\Throwable $e) {
            $response = array(
                'error' => true,
                'message' => $e
            );
        }
        return response()->json($response);

    }
    public function getRadioStationByCategory($category_id)
    {
        try {
            $radioStation = RadioStation::where('category_id',$category_id)->get();
            $response = array(
                'error' => false,
                'message' => 'Radio Station Fetched Successfully',
                'data' => $radioStation
            );

        } catch (\Throwable $e) {
            $response = array(
                'error' => true,
                'message' => $e
            );
        }
        return response()->json($response);
    }

}
