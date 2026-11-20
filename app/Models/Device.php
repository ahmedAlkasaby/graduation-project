<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

use DB;

class Device extends SiteModel
{

    protected $fillable = [
        'user_id', 'imei', 'token', 'type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function insertDevice($user_id, $imei,$token,$type = NULL){
        $this->user_id      = $user_id;
        $this->imei         = $imei;
        $this->token        = $token;
        $this->type         = $type;
        return $this->save();
    }

    public static function updateDevice($user_id, $imei,$token,$type = NULL) {
        $device_update = static::where('user_id', $user_id)->where('imei', $imei)->first();
        if (isset($device_update)) {
            $device_update->token = $token;
            $device_update->type  = $type;
            return $device_update->save();
        } else {
            $device_insert = new Device();
            return $device_insert->insertDevice($user_id, $imei,$token,$type);
        }

    }

    public static function foundDevice($user_id, $imei) {
        $device_found = static::where('user_id', $user_id)->where('imei', $imei)->first();
        if (isset($device_found)) {
            return $device_found->id;
        } else {
            return 0;
        }

    }

    public static function deleteAllDevice($user_id) {
        return static::where('user_id', $user_id)->delete();
    }

    public static function deleteAllOtherDevice($user_id,$imei) {
        return static::where('user_id', $user_id)->where('imei','<>', $imei)->delete();
    }

    public static function deleteDevice($user_id, $imei) {
        return static::where('user_id', $user_id)->where('imei', $imei)->delete();
    }
}
