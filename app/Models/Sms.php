<?php

namespace App\Models;

class Sms extends SiteModel
{
    protected $table = 'smses';
    protected $fillable = ['name','active','sender_name','key' ,'user_name' , 'password','uid'];
}
