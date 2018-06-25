<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Validator;
use App\Models\Supervisor;

class SupervisorsController extends ApiController {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        try {
            $user = $this->auth_user();
            $where_array = array();
            if ($request->input('search')) {
                $where_array['search'] = $request->input('search');
            }

            $supervisors = Supervisor::getAll($where_array);

            return _api_json($supervisors);
        } catch (\Exception $e) {
            $message = _lang('app.error_is_occured');
            return _api_json([], ['message' => $e->getMessage()], 400);
        }
    }
   

}
