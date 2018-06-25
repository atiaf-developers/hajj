<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends MyModel {

    protected $table = "locations";

    public static function getAll() {
        return static::join('locations_translations as trans', 'locations.id', '=', 'trans.location_id')
                        ->select('locations.id', "trans.title")
                        ->orderBy('locations.this_order', 'ASC')
                        ->where('locations.parent_id',0)
                        ->where('trans.locale', static::getLangCode())
                        ->get();
    }

    public function childrens() {
        return $this->hasMany(Location::class, 'parent_id');
    }

    public function translations() {
        return $this->hasMany(LocationTranslation::class, 'location_id');
    }

    public function supervisor() {
        return $this->belongsTo(Supervisor::class, 'supervisor_id');
    }
    
    public static function transform($item) {
     


        return $item;
    }

    protected static function boot() {
        parent::boot();

        static::deleting(function($location) {
            foreach ($location->childrens as $child) {
                foreach ($child->translations as $translation) {
                    $translation->delete();
                }
                $child->delete();
            }

            foreach ($location->translations as $translation) {
                $translation->delete();
            }
        });
        static::deleted(function($location) {
            $location->supervisor->delete();
        });
    }

}
