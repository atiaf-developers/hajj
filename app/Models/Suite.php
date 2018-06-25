<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suite extends MyModel {

    protected $table = 'suites';
    protected $casts = array(
    );

    public function lounges() {
        return $this->hasMany(Lounge::class, 'suite_id');
    }

    public static function transform($item) {
        return $item;
    }

}
