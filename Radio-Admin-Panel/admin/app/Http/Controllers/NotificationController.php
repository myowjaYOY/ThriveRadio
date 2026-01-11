<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\City;
use App\Models\Category;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cities = City::all();
        $categories = Category::all();
        $city_mode = collect(getSettings('city_mode'))->isNotEmpty() ? getSettings('city_mode')['city_mode'] : 1 ;

        return response(view('notifications.index',compact('cities','city_mode', 'categories')));
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
            'title' => 'required',
            'city_id' => 'numeric|nullable',
            'category_id' => 'required',
            'radio_station_id' => 'required',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }
        try{
            $date = Carbon::now();
            $notification = new Notification();
            $notification->title = $request->title;
            $notification->city_id = $request->city_id;
            $notification->category_id = $request->category_id;
            $notification->radio_station_id = $request->radio_station_id;
            $notification->message = $request->message;
            $notification->date = $date;

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $file_name = time() . '.' . $image->getClientOriginalExtension();
                $file_path = 'notifications/' . $file_name;
                $destinationPath = storage_path('app/public/notifications');
                $image->move($destinationPath, $file_name);
                $notification->image = $file_path;
            }

            $notification->save();

            $notification = Notification::where('id', $notification->id)->first();
            $title = $notification->title;
            $body = $notification->message;
            $image = $notification->image ?? null;
            $type = "Custom";
            $category =  $notification->category->name;
            $city = $notification->city->name ?? null;
            $radio_station = $notification->radioStation->name;

            send_notification($title, $body, $type, $image, $category, $city, $radio_station);

            $response = array(
                'error' => false,
                'message' => 'Notification Sent Sucessfully'
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
    public function show($id)
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

        $sql = Notification::with('city','category','radioStation');

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
            $operate = '<a href='.route('notification.destroy',$row->id).' class="btn icon btn-danger delete-form deletebutton" data-id=' . $row->id . '><i class="fa fa-trash"></i></a>';

            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['sequence'] = $row->sequence;
            $tempRow['city_id'] = $row->city_id ?? '';
            $tempRow['city'] = $row->city->name ?? '-';
            $tempRow['category_id'] = $row->category_id;
            $tempRow['category'] = $row->category->name;
            $tempRow['radio_station_id'] = $row->radio_station_id;
            $tempRow['radiostation'] = $row->radioStation->name;
            $tempRow['title'] = $row->title;
            $tempRow['image'] = $row->image;
            $tempRow['date'] = $row->date;
            $tempRow['operate'] = $operate;
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $notification = Notification::find($id);
            if($notification->image != null)
            {
                if (Storage::disk('public')->exists($notification->getRawOriginal('image'))) {
                    Storage::disk('public')->delete($notification->getRawOriginal('image'));
                }
            }

            $notification->delete();
            $response = array(
                'error' => false,
                'message' => 'Notification Deleted Successfully'
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
