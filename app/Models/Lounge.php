<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lounge extends MyModel {

    protected $table = 'lounges';
    protected $fillable=array('number','suite_id','available_of_accommodation','gender');
    protected $casts = array(
     
    );
    	

    public static function transform($item) {
        return $item;
    }
    

}
