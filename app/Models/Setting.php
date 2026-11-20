<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;


class Setting extends Model

{

    protected $table = 'settings';
//    public $timestamps = false;
    protected $fillable = [
        'group','type','key', 'value','locale','autoload','parent_id'
    ];

    public function insertSetting($type, $key,$value,$autoload = 1,$group = NULL,$locale = 'ar',$parent_id = NULL) {

        $this->type    = $type;
        $this->key     = $key;
        $this->value   = $value;
        $this->group   = $group;
        $this->locale            = $locale;
        $this->autoload        = $autoload;
        $this->parent_id       = $parent_id;
        return $this->save();
    }

    public static function updateSetting($key,$value,$autoload =  1,$group = NULL,$locale = 'ar',$parent_id = NULL) {
        $setting = static::where('type', $key)->where('key', $key)->first();
        if (isset($setting)) {
            $setting->value   = $value;
            $setting->group   = $group;
            $setting->autoload        = $autoload;
            $setting->parent_id       = $parent_id;
            return $setting->save();
        } else {
            $insert = new Setting();
            return $insert->insertSetting($key, $key,$value,$autoload,$group,$locale,$parent_id);
        }

    }

    public static function updateSettinglocale($key,$value,$autoload =  1,$group = NULL,$locale = 'ar',$parent_id = NULL) {
        $setting = static::where('type', $key)->where('key', $key)->where('locale', $locale)->first();
        if (isset($setting)) {
            $setting->value   = $value;
            $setting->group   = $group;
            $setting->autoload        = $autoload;
            $setting->parent_id       = $parent_id;
            return $setting->save();
        } else {
            $insert = new Setting();
            return $insert->insertSetting($key, $key,$value,$autoload,$group,$locale,$parent_id);
        }

    }

    public function deleteSetting($type) {
        return static::where('type', $type)->delete();

    }

    public function deleteSettingGroup($group) {
        return static::where('group', $group)->delete();

    }

    public function deleteSettingParent($parent_id) {
        return static::where('parent_id', $parent_id)->delete();

    }

    public function deleteSettingLocale($type,$locale = 'ar') {
        return static::where('type', $type)->where('locale', $locale)->delete();

    }

    public function deleteSettingGroupLocale($group,$locale = 'ar') {
        return static::where('group', $group)->where('locale', $locale)->delete();

    }

    public function deleteSettingParentLocale($parent_id,$locale = 'ar') {
        return static::where('parent_id', $parent_id)->where('locale', $locale)->delete();

    }
    public static function get($key,$default = NULL){
        return static::where('key', $key)->first()->value ?? $default;
    }
}


