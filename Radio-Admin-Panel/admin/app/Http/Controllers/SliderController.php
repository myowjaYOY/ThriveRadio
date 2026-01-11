<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Slider;
use App\Models\Category;
use App\Models\RadioStation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cities = City::all();
        $categories = Category::all();
        $radiostations = RadioStation::all();
        $city_mode = collect(getSettings('city_mode'))->isNotEmpty() ? getSettings('city_mode')['city_mode'] : 1 ;

        return response(view('sliders.index',compact('cities','categories','radiostations','city_mode')));
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
        try {
            $maxSequence = Slider::max('sequence');
            $newSequence = $maxSequence + 1;

            $slider = new Slider();
            $slider->title = $request->title;
            $slider->city_id = $request->city_id;
            $slider->category_id = $request->category_id;
            $slider->radio_station_id = $request->radio_station_id;

            if($request->hasFile('image'))
            {
                $image = $request->file('image');
                $file_name = time() . '-' . $image->getClientOriginalName();
                $file_path = 'sliders/' . $file_name;
                $destinationPath = storage_path('app/public/sliders');
                $image->move($destinationPath, $file_name);
                $slider->image = $file_path;
            }
            $slider->sequence = $newSequence;
            $slider->save();

            $response = array(
                'error' => false,
                'message' => 'Slider Added Sucessfully'
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

        $sql = Slider::with('city','category','radioStation');

        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $search = $_GET['search'];
            $sql->where('id', 'LIKE', "%$search%")->orwhere('title', 'LIKE', "%$search%")
            ->orWhereHas('city', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%");
            })->orWhereHas('category', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%");
            })->orWhereHas('radioStation', function ($q) use ($search) {
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
            $operate = '<a href='.route('slider.update',$row->id).' class="btn icon btn-primary edit editbutton" data-id=' . $row->id . '" title="Edit" data-bs-toggle="modal" data-bs-target="#editModal"><i class="fa fa-edit"></i></a>';
            $operate .= '<a href='.route('slider.destroy',$row->id).' class="btn icon btn-danger delete-form deletebutton" data-id=' . $row->id . '><i class="fa fa-trash"></i></a>';

            $tempRow['id'] = $row->id ??'';
            $tempRow['no'] = $no++ ??'';
            $tempRow['sequence'] = $row->sequence ??'';
            $tempRow['city_id'] = $row->city_id ?? '';
            $tempRow['city'] = $row->city->name ?? '-';
            $tempRow['category_id'] = $row->category_id ?? '';
            $tempRow['category'] = $row->category->name ?? '';
            $tempRow['radio_station_id'] = $row->radio_station_id ?? '';
            $tempRow['radiostation'] = $row->radioStation->name ?? '';
            $tempRow['title'] = $row->title ??'';
            $tempRow['image'] = $row->image ??'';
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
        try {
            $maxSequence = Slider::max('sequence');
            $newSequence = $maxSequence + 1;

            $slider = Slider::find($request->id);
            $slider->title = $request->title;
            $slider->city_id = $request->city_id;
            $slider->category_id = $request->category_id;
            $slider->radio_station_id = $request->radio_station_id;

            if($request->hasFile('image'))
            {
                if (Storage::disk('public')->exists($slider->getRawOriginal('image'))) {
                    Storage::disk('public')->delete($slider->getRawOriginal('image'));
                }

                $image = $request->file('image');
                $file_name = time() . '-' . $image->getClientOriginalName();
                $file_path = 'sliders/' . $file_name;
                $destinationPath = storage_path('app/public/sliders');
                $image->move($destinationPath, $file_name);
                $slider->image = $file_path;
            }
            $slider->sequence = $newSequence;
            $slider->save();

            $response = array(
                'error' => false,
                'message' => 'Slider Updated Sucessfully'
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
            $slider = Slider::find($id);
            if (Storage::disk('public')->exists($slider->getRawOriginal('image'))) {
                Storage::disk('public')->delete($slider->getRawOriginal('image'));
            }
            $slider->delete();
            $response = array(
                'error' => false,
                'message' => 'Slider Deleted Successfully'
            );
        } catch (\Throwable $e) {
            $response = array(
                'error' => true,
                'message' => $e
            );
        }
        return response()->json($response);
    }

    public function changeSequence(Request $request)
    {
        try {
            $ids = $request->ids;
            $update = [];

            foreach ($ids as $key => $id) {
                $update[] = [
                    'id' => $id,
                    'sequence' => ($key + 1)
                ];
            }
            Slider::upsert($update, ['id'], ['sequence']);

            $response = [
                'error' => false,
                'message' => "Sequence Updated Successfully"
            ];
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
