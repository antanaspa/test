<?php

namespace App;

use Config;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\DeviceLocation
 *
 * @property int $id
 * @property string $location
 * @property string|null $address_string
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\DeviceLocationLog $locationLog
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DeviceLocation comparison($geometryColumn, $geometry, $relationship)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DeviceLocation contains($geometryColumn, $geometry)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DeviceLocation crosses($geometryColumn, $geometry)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DeviceLocation disjoint($geometryColumn, $geometry)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DeviceLocation distance($geometryColumn, $geometry, $distance)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DeviceLocation distanceExcludingSelf($geometryColumn, $geometry, $distance)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DeviceLocation distanceSphere($geometryColumn, $geometry, $distance)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DeviceLocation distanceSphereExcludingSelf($geometryColumn, $geometry, $distance)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DeviceLocation distanceSphereValue($geometryColumn, $geometry)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DeviceLocation distanceValue($geometryColumn, $geometry)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DeviceLocation doesTouch($geometryColumn, $geometry)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DeviceLocation equals($geometryColumn, $geometry)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DeviceLocation intersects($geometryColumn, $geometry)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DeviceLocation overlaps($geometryColumn, $geometry)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DeviceLocation whereAddressString($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DeviceLocation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DeviceLocation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DeviceLocation whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DeviceLocation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DeviceLocation within($geometryColumn, $polygon)
 * @mixin \Eloquent
 */
class DeviceLocation extends Model
{
    use SpatialTrait;

    protected $spatialFields = [
        'location',
    ];

    protected $table = 'device_location';

    const API_URL = 'https://maps.googleapis.com/maps/api/geocode/json?&latlng=';

    public function coordToAddress()
    {

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => self::API_URL . $this->location->getLat() . ',' . $this->location->getLng() . '&key=' . Config::get('settings.google_api_key')
        ));

        $result = json_decode(curl_exec($curl));

        if (isset($result->results[0]))
            return $result->results[0]->formatted_address;

        return '';

    }

    public function locationLog()
    {
        return $this->belongsTo(DeviceLocationLog::class, 'id', 'point_id');

    }


}
