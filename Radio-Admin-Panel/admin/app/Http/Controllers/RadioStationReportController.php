<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RadioStationReport;
use Illuminate\Support\Facades\Auth;

class RadioStationReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response(view('radio_station_report.index'));
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
        //
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

        $sql = RadioStationReport::with('radioStation');

        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $search = $_GET['search'];
            $sql->where('id', 'LIKE', "%$search%")
            ->orWhereHas('radioStation', function ($q) use ($search) {
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
            $operate = '<a href='.route('radio-station-report.destroy',$row->id).' class="btn icon btn-danger delete-form deletebutton" data-id=' . $row->id . '><i class="fa fa-trash"></i></a>';

            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['radio_station_id'] = $row->radio_station_id;
            $tempRow['radiostation'] = $row->radioStation->name;
            $tempRow['message'] = $row->message;
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
            $reported = RadioStationReport::find($id);
            $reported->delete();
            $response = array(
                'error' => false,
                'message' => 'Radio Station Report Deleted Successfully'
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
