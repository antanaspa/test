<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\DeviceLocationLog
 *
 * @property int $id
 * @property int $device_id
 * @property int $point_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Device $device
 * @property-read \App\DeviceLocation $deviceLocation
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DeviceLocationLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DeviceLocationLog whereDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DeviceLocationLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DeviceLocationLog wherePointId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\DeviceLocationLog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DeviceLocationLog extends Model
{

    public function deviceLocation()
    {
        return $this->hasOne(DeviceLocation::class, 'id', 'point_id');

    }



    public function device()
    {
        return $this->belongsTo(Device::class);
    }

}
