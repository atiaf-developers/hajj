<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PilgrimClass extends MyModel {

    protected $table = "pilgrims_class";

    public static function getAll() {
        return static::join('pilgrims_class_translations as trans', 'pilgrims_class.id', '=', 'trans.pilgrims_class_id')
                        ->select('pilgrims_class.id', "trans.title")
                        ->orderBy('pilgrims_class.this_order', 'ASC')
                        ->where('trans.locale', static::getLangCode())
                        ->get();
    }

    public function translations() {
        return $this->hasMany(PilgrimClassTranslation::class, 'pilgrims_class_id');
    }

    public function supervisor() {
        return $this->belongsTo(Supervisor::class, 'supervisor_id');
    }

    protected static function boot() {
        parent::boot();

        static::deleting(function($pilgrim_class) {
            foreach ($pilgrim_class->translations as $translation) {
                $translation->delete();
            }
        });
        static::deleted(function($pilgrim_class) {
            $pilgrim_class->supervisor->delete();
        });
    }

}
