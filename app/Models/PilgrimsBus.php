<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PilgrimsBus extends MyModel {

    protected $table = "pilgrims_buses";

  
    public function supervisor() {
        return $this->belongsTo(Supervisor::class, 'supervisor_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
    
   

    protected static function boot() {
        parent::boot();

        static::deleted(function($pilgrims_bus) {
            $pilgrims_bus->supervisor->delete();
            $pilgrims_bus->user->delete();
        });
    }

}
