<?php

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;

class SiteModelSoftDelete extends SiteModel {

    use SoftDeletes;
    protected $dates = ['deleted_at'];

}
