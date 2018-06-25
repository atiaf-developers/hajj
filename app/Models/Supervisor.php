<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supervisor extends MyModel {

    protected $table = "supervisors";

    public static function getAll($where_array = array()) {
        $supervisors = static::join('pilgrims_buses', 'supervisors.id', '=', 'pilgrims_buses.supervisor_id');
        $supervisors->select(['supervisors.id', "pilgrims_buses.bus_number", "supervisors.name", "supervisors.supervisor_image",
           "supervisors.contact_numbers",]);
        

        if (!empty($where_array)) {
                foreach ($where_array as $key => $value) {
                    if ($key == 'search') {
                        $supervisors->whereRaw(static::handleKeywordWhere(['supervisors.name'], $value));
                    } else {
                        $supervisors->where($key, $value);
                    }
                }
            }
        $supervisors->orderBy('supervisors.created_at', 'DESC');
        $supervisors = $supervisors->paginate(static::$limit);
        $supervisors = $supervisors->getCollection()->transform(function($supervisor, $key) {
            return static::transformTwo($supervisor);
        });

        return $supervisors;
    }

    public static function transform($item) {
        $transformer = new \stdClass();
        $transformer->id = $item->id;
        $transformer->name = $item->name;
        $transformer->image = url('public/uploads/supervisors') . '/' . $item->supervisor_image;
        $transformer->job = $item->job;
        $transformer->contact_numbers = explode(",", $item->contact_numbers);


        return $transformer;
    }

    public static function transformSetting($item) {
        $transformer = new \stdClass();
        $transformer->name = $item->name;
        $transformer->image = url('public/uploads/supervisors') . '/' . $item->image;
        $transformer->contact_numbers = explode(",", $item->contact_numbers);
        return $transformer;
    }


    public static function transformTwo($item) {
        $transformer = new \stdClass();
        $transformer->id = $item->id;
        $transformer->bus_number = $item->bus_number;
        $transformer->name = $item->name;
        $transformer->image = url('public/uploads/supervisors') . '/' . $item->supervisor_image;
        $transformer->contact_numbers = explode(",", $item->contact_numbers);


        return $transformer;
    }

    protected static function boot() {
        parent::boot();

        static::deleting(function($supervisor) {
            $old_image = $supervisor->supervisor_image;
            Supervisor::deleteUploaded('supervisors', $old_image);
        });
    }

}
