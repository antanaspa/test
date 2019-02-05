<?php

namespace App\Http\Controllers;

use App\Device;
use App\DeviceLocation;
use App\DeviceLocationLog;
use App\DevicePointLog;
use App\Mail\WorkDeviceNotification;
use DB;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class DeviceController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth')->except(['create','store']);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pointCollection = [];

        foreach (DeviceLocation::with('locationLog')->get() as $location) {
            $pointCollection[$location->locationLog->device->id] = $location->locationLog->device->device_id;
        };



        return View::make('device.index')
            ->with('pointCollection', $pointCollection);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return View::make('device.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' =>
                [
                    'required',
                    'max:255',
                    'min:1',
                    'regex:/(^[A-Za-z0-9 ]+$)+/',
                ],
                'lat' => [
                'required',
                'regex:/^(\+|-)?(?:90(?:(?:\.0{1,6})?)|(?:[0-9]|[1-8][0-9])(?:(?:\.[0-9]{1,6})?))$/'
                ],
                'lng' => [
                    'required',
                    'regex:/^(\+|-)?(?:180(?:(?:\.0{1,6})?)|(?:[0-9]|[1-9][0-9]|1[0-7][0-9])(?:(?:\.[0-9]{1,6})?))$/'
                ]
        ]);

        if ($validator->fails()) {
            return redirect('device/create')
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();
            $deviceId = $request->get('device_id');

            if (!$device = Device::whereDeviceId($deviceId)->first()) {
                $device = new Device($request->except('_token'));
                $device->save();
            }

            $point = new Point($request->get('lat'), $request->get('lng'));
            if (!$deviceLocation = DeviceLocation::whereRaw("location = ST_GeomFromText('{$point->toWkt()}')")->first()) {
                $deviceLocation = new DeviceLocation();
                $deviceLocation->location = $point;
                $deviceLocation->address_string = $deviceLocation->coordToAddress();
                $deviceLocation->save();
            }

            $deviceLocationLog = new DeviceLocationLog();
            $deviceLocationLog->device_id = $device->id;
            $deviceLocationLog->point_id = $deviceLocation->id;
            $deviceLocationLog->save();


            if ($device->type == Device::TYPE_WORK) {
                \Mail::to(\Config::get('settings.notifyEmail'))->send(new WorkDeviceNotification($device->device_id, $deviceLocation->address_string));
            }
            DB::commit();
        } catch (\Exception $e){
            return redirect()->back()->with('message', ['type' => 'danger', 'text' => $e->getMessage()]);

        }

        return redirect()->back()->with('message', ['type' => 'success', 'text' =>'Location added']);
    }





    /**
     * Get device list
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


    public function getList(Request $request)
    {

        $pointCollection = null;
        $response = ['success' => true, 'data' => Device::all(['id','device_id'])->toArray()];


        return new JsonResponse($response);

    }


    /**
     * Get current locations
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function getLocation(Request $request)
    {
        $locationCollection = null;
        $response = ['success' => false, 'data' => $locationCollection];
        $deviceToShow = $request->get('device');
        if (!$deviceToShow) {
            return $response;
        }

        try {
            $response['success'] = true;
            $maxDistance = 0;
            $distance = [];
            $locationCollection = Device::whereIn('id', $deviceToShow)
                ->with(['latestLocation', 'locationLog'])
                ->get()
                ->toArray();

            // could be done more elegantly, but i'm already tired, so this will do.
            $ids = [];
            foreach ($locationCollection as  $temp) {
                $ids[] = $temp['latest_location']['point_id'];
            }

            foreach ($locationCollection as $k => $location) {
                $popupView = View::make('device.popup', ['location' => $location]);
                $locationCollection[$k]['popupContent'] = $popupView->render();

                $info = DeviceLocation::whereIn('id',$ids)->selectRaw("ROUND(ST_Distance(`location`, ST_GeomFromText('{$location['latest_location']['device_location']['location']->toWkt()}'))*67.22) as distance, id")->with('locationLog')->orderBy('distance', 'DESC')->first();
                if ($info->distance > $maxDistance) {
                    $maxDistance = $info->distance;
                    $distance['value']     = $info->distance;
                    $distance['device']    = $location['device_id'];
                    $distance['from']      = $info->locationLog->device->device_id;
                }
            }

            $response['data']['device'] = $locationCollection;
            $response['data']['distance'] = $distance;

        } catch (\Exception $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }

        return new JsonResponse($response);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
