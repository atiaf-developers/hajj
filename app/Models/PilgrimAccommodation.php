<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PilgrimAccommodation extends MyModel {

    protected $table = "pilgrims_accommodation";
    public static $types = [
        'suite_lounge_seat',
        'suite_lounge_chair',
        'suite_lounge_bead',
        'suite_lounge',
    ];

}
