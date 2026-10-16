<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Model;


class Attachment extends SiteModelDefault
{

    protected $table = 'attachments';
    protected $fillable = [
        'name', 'user_id', 'attachmentable_id', 'attachmentable_type', 'type', 'key', 'value'
    ];

    public function attachmentable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::Class)->withTrashed();
    }

    public function users()
    {
        return $this->morphedByMany(User::Class, 'attachmentable')->withTrashed();
    }

    public function products()
    {
        return $this->morphedByMany(Product::Class, 'attachmentable')->withTrashed();
    }

    public function categories()
    {
        return $this->morphedByMany(Category::Class, 'attachmentable')->withTrashed();
    }

    public function pages()
    {
        return $this->morphedByMany(Page::Class, 'attachmentable')->withTrashed();
    }


    public function insertattachment($user_id, $attachmentable_id, $attachmentable_type, $type, $key, $value)
    {

        $this->user_id = $user_id;
        $this->attachmentable_id = $attachmentable_id;
        $this->attachmentable_type = $attachmentable_type;
        $this->type  = $type;
        $this->key   = $key;
        $this->value = $value;
        return $this->save();
    }

    public static function updateattachment($user_id, $attachmentable_id, $attachmentable_type, $type, $key, $value)
    {
        $attachment = static::where('user_id', $user_id)->where('attachmentable_id', $attachmentable_id)->where('attachmentable_type', $attachmentable_type)->where('type', $type)->first();
        $attachment_id = 0;
        if (isset($attachment)) {
            $attachment->key = $key;
            $attachment->value = $value;
            $attachment->save();
            $attachment_id = $attachment->id;
        } else {
            $insert = new attachment();
            $insert->insertattachment($user_id, $attachmentable_id, $attachmentable_type, $type, $key, $value);
            $attachment_id = $insert->id;
        }
        return $attachment_id;
    }

    public static function deleteattachmentable($attachmentable_id, $attachmentable_type)
    {
        return static::where('attachmentable_id', $attachmentable_id)->where('attachmentable_type', $attachmentable_type)->delete();
    }

    public static function deleteattachment($attachmentable_id, $attachmentable_type, $type)
    {
        return static::where('attachmentable_id', $attachmentable_id)->where('attachmentable_type', $attachmentable_type)->where('type', $type)->delete();
    }

    public static function foundattachment($user_id, $attachmentable_id, $attachmentable_type, $type)
    {
        $attachment = static::where('user_id', $user_id)->where('attachmentable_id', $attachmentable_id)->where('attachmentable_type', $attachmentable_type)->where('type', $type)->first();
        if (isset($attachment)) {
            return $attachment->id;
        } else {
            return 0;
        }
    }
}
