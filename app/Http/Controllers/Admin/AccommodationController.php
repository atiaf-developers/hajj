<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\BackendController;
use App\Models\Location;
use App\Models\PilgrimClass;

class AccommodationController extends BackendController {

    private $rules = array(
        'android_url' => 'required|url', 'ios_url' => 'required|url',
       
    );

    public function index() {
        $this->data['locations']=Location::getAll();
        $this->data['pilgrims_class']= PilgrimClass::getAll();
        return $this->_view('accommodation/index', 'backend');
    }

   

}
