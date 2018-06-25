<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Validator;
use App\Models\Pilgrim;

class PilgrimsController extends ApiController {

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
            //dd($user->supervisor);
            if ($user->type == 2) {   //supervisors
                $where_array['pilgrims_buses.id'] = $user->bus->id;
            }

            if ($request->input('location')) {
                $where_array['locations.id'] = json_decode($request->input('location'));
            }
            if ($request->input('supervisor')) {
                $where_array['s1.id'] = $request->input('supervisor');
            }
            if ($request->input('search')) {
                $where_array['search'] = $request->input('search');
            }

            //dd($where_array);
            $pilgrims = Pilgrim::getAll($where_array);

            return _api_json($pilgrims);
        } catch (\Exception $e) {
            $message = _lang('app.error_is_occured');
            return _api_json([], ['message' => $e->getMessage()], 422);
        }
    }
    public function show($id) {
        try {
            $user = $this->auth_user();
            $where_array['pilgrims.id'] = $id;
            
            $pilgrims = Pilgrim::getAll($where_array);

            return _api_json($pilgrims);
        } catch (\Exception $e) {
            $message = _lang('app.error_is_occured');
            return _api_json([], ['message' => $e->getMessage()], 422);
        }
    }

}
