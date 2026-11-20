<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Model;

class UserMeta extends SiteModel
{
    protected $table = 'user_metas';

    protected $fillable = [
        'user_id', 'type', 'key', 'value', 'group', 'parent_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'address_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'parent_id');
    }

    public function insertMeta($user_id, $type, $key, $value, $group = null, $parent_id = null)
    {
        $this->user_id = $user_id;
        $this->type = $type;
        $this->key = $key;
        $this->value = $value;
        $this->group = $group;
        $this->parent_id = $parent_id;

        return $this->save();
    }

    public static function foundMeta($user_id, $type, $value)
    {
        $meta = static::where('user_id', $user_id)->where('type', $type)->where('key', $type)->where('value', $value)->first();
        if (isset($meta)) {
            return $meta->id;
        } else {
            return 0;
        }
    }

    public function updateMeta($user_id, $type, $key, $value, $group = null, $parent_id = null)
    {
        $meta = static::where('user_id', $user_id)->where('type', $type)->where('key', $key)->first();
        if (isset($meta)) {
            $this->value = $value;

            return $meta->save();
        } else {
            $insert = new UserMeta;

            return $insert->insertMeta($user_id, $type, $key, $value, $group = null, $parent_id = null);
        }
    }

    public static function deleteMeta($id, $user_id)
    {
        return static::where('id', $id)->where('user_id', $user_id)->delete();
    }

    public static function updateMetaValue($id, $user_id, $value)
    {
        return static::where('id', $id)->where('user_id', $user_id)->update(['value' => $value]);
    }

    public static function deleteMetaType($user_id, $type)
    {
        return static::where('user_id', $user_id)->where('type', $type)->delete();
    }
}
