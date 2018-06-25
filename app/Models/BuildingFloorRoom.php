<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuildingFloorRoom extends MyModel {

    protected $table = 'buildings_floors_rooms';
    //protected $fillable=array('number','suite_id','available_of_accommodation','gender');
    protected $casts = array(
     
    );
    	

    public static function transform($item) {
        return $item;
    }
    

}
