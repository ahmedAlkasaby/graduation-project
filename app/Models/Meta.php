<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Model;


class Meta extends SiteModel {

    protected $table = 'metas';
    protected $fillable = [
        'metaable_id', 'metaable_type','type', 'key', 'value', 'group','parent_id'
    ];

    public function metaable()
    {
        return $this->morphTo();
    }

    public function childrens() {
        return $this->hasMany(Meta::Class, 'parent_id');
    }

    public function parent() {
        return $this->belongsTo(Meta::Class, 'parent_id');
    }

    public function users()
    {
        return $this->morphedByMany(User::Class, 'metaable')->withTrashed();
    }

    public function products()
    {
        return $this->morphedByMany(Product::Class, 'metaable')->withTrashed();
    }

    public function pages()
    {
        return $this->morphedByMany(Page::Class, 'metaable')->withTrashed();
    }

    public function categories()
    {
        return $this->morphedByMany(Category::Class, 'metaable')->withTrashed();
    }

    public function orders()
    {
        return $this->morphedByMany(Order::Class, 'metaable');
    }

    public function insertMeta($metaable_id,$metaable_type, $type, $key, $value , $group = NULL,$parent_id = NULL) {

        $this->metaable_id = $metaable_id;
        $this->metaable_type = $metaable_type;
        $this->type  = $type;
        $this->key   = $key;
        $this->value = $value;
        $this->group = $group;
        $this->parent_id  = $parent_id;
        return $this->save();
    }

    public function updateMeta($metaable_id,$metaable_type, $type, $key, $value,$group = NULL,$parent_id = NULL) {
        $meta = static::where('metaable_id', $metaable_id)->where('metaable_type', $metaable_type)->where('type', $type)->first();
        if (isset($meta)) {
            $meta->key   = $key;
            $meta->value = $value;
            $meta->group = $group;
            $meta->parent_id  = $parent_id;
            return $meta->save();
        } else {
            $insert =  new Meta();
            return $insert->insertMeta($metaable_id,$metaable_type, $type, $key, $value,$group,$parent_id);
        }
    }

    public static function deleteMeta($metaable_id,$metaable_type, $type) {
        return static::where('metaable_id', $metaable_id)->where('metaable_type', $metaable_type)->where('type', $type)->delete();
    }

}
