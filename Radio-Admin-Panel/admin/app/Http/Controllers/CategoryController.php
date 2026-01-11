<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = getSettings();
        return response(view('category.index'));
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
            'name' => 'required|unique:categories|string',
            'image' => 'mimes:jpeg,png,jpg|image|max:2048'
        ]);

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }
        try{
            $category = new Category();
            $category->name = $request->name;
            if($request->hasFile('image'))
            {
                $image = $request->file('image');
                $file_name = time() . '-' . $image->getClientOriginalName();
                $file_path = 'category/' . $file_name;
                $destinationPath = storage_path('app/public/category');
                $image->move($destinationPath, $file_name);
                $category->image = $file_path;
            }
            $category->save();

            $response = array(
                'error' => false,
                'message' => 'Category Added Sucessfully'
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

        $sql = Category::where('id', '!=', 0);

        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $search = $_GET['search'];
            $sql->where('id', 'LIKE', "%$search%")->orwhere('name', 'LIKE', "%$search%");
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
            $operate = '<a href='.route('category.update',$row->id).' class="btn icon btn-primary edit editbutton" data-id=' . $row->id . '" title="Edit" data-bs-toggle="modal" data-bs-target="#editModal"><i class="fa fa-edit"></i></a>';
            $operate .= '<a href='.route('category.destroy',$row->id).' class="btn icon btn-danger delete-form deletebutton" data-id=' . $row->id . '><i class="fa fa-trash"></i></a>';

            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['name'] = $row->name;
            $tempRow['image'] = $row->image;
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
            'edit_name' => 'required|unique:categories,name,'.$request->edit_id,
            'image' => 'mimes:jpeg,png,jpg|image|max:2048'
        ]);

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }
        try {
            $category = Category::find($request->edit_id);
            $category->name = $request->edit_name;
            if($request->hasFile('image'))
            {
                if($category->image != null)
                {
                    if (Storage::disk('public')->exists($category->getRawOriginal('image'))) {
                        Storage::disk('public')->delete($category->getRawOriginal('image'));
                    }
                }
                $image = $request->file('image');
                $file_name = time() . '-' . $image->getClientOriginalName();
                $file_path = 'category/' . $file_name;
                $destinationPath = storage_path('app/public/category');
                $image->move($destinationPath, $file_name);
                $category->image = $file_path;
            }
            $category->save();
            $response = array(
                'error' => false,
                'message' => "Category Updated Sucessfully",
            );
        } catch (\Throwable $e) {
            $response = array(
                'error' => true,
                'message' => $e,
            );
        }
        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $category = Category::find($id);
            $category->delete();
            $response = array(
                'error' => false,
                'message' => trans('City Deleted Successfully')
            );
        } catch (\Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred')
            );
        }
        return response()->json($response);
    }


}
