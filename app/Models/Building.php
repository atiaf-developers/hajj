<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Building extends MyModel {

    protected $table = 'buildings';
    protected $casts = array(
    );

    public function floors() {
        return $this->hasMany(BuildingFloor::class, 'building_id');
    }

    public static function transform($item) {
        return $item;
    }

}
