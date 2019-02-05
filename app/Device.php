<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Device
 *
 * @property int $id
 * @property string $device_id
 * @property string $type
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\DeviceLocationLog[] $locationLog
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Device whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Device whereDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Device whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Device whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Device whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Device extends Model
{
    const TYPE_HOME = 0;
    const TYPE_WORK = 1;

    protected $table = 'device';

    protected $fillable = ['type', 'device_id'];

    public static function getTypeCollection()
    {
        return [self::TYPE_HOME => 'Home', self::TYPE_WORK => 'Work'];
    }

    public function locationLog()
    {
        return $this->hasMany(DeviceLocationLog::class, 'device_id', 'id');
    }

    public function latestLocation()
    {
        return $this
                ->hasOne(DeviceLocationLog::class)
                ->with('deviceLocation')
                ->latest();
    }
}
