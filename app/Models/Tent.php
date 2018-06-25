<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tent extends MyModel {

    protected $table = 'tents';
    protected $fillable=array('number','type','available_of_accommodation','gender');
    protected $casts = array(
     
    );
    	

    public static function transform($item) {
        return $item;
    }
    

}
