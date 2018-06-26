<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends MyModel {

    protected $table = "devices";
    protected $fillable = ['device_id','device_token','device_type','updated_at'];

}
