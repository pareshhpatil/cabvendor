<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Lib\Encryption;
use App\Models\ParentModel;

class TripController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $model;
    public function __construct()
    {
        $this->model = new ParentModel();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function rideLiveTrack(Request $request, $ride_id)
    {
        $response = $this->model->updateTable('ride_live_location', 'ride_id', $ride_id, 'live_location', json_encode($request->all()));
        if ($response == false) {
            $array['live_location'] = json_encode($request->all());
            $this->model->saveTable('ride_live_location', $array);
        }
    }

    public function rideLocation($ride_id)
    {
        $array = $this->model->getColumnValue('ride_live_location', 'ride_id', $ride_id, 'live_location');
        return response()->json($array);
    }
}
