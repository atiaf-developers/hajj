<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminNotification extends MyModel {

    protected $table = 'admin_notifications';

      public static function transform($item) {
        return $item;
    }
}
