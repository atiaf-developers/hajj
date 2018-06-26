<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\BuildingFloor;
use App\Models\BuildingFloorRoom;
use Validator;
use DB;

class BuildingsFloorsRoomsController extends BackendController {

    private $rules = array(
        'number' => 'required',
        'available_of_accommodation' => 'required',
    );

    public function __construct() {
        parent::__construct();
        $this->middleware('CheckPermission:buildings_floors_rooms,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:buildings_floors_rooms,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:buildings_floors_rooms,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:buildings_floors_rooms,delete', ['only' => ['delete']]);
    }

    public function index(Request $request) {
        $BuildingFloor = BuildingFloor::find($request->input('floor'));
        if (!$BuildingFloor) {
            return $this->err404();
        }
        $this->data['floor'] = $BuildingFloor;
        return $this->_view('buildings_floors_rooms/index', 'backend');
    }

    public function create() {
        return $this->_view('buildings_floors_rooms/create', 'backend');
    }

    public function store(Request $request) {
        $floor_id = $request->input('floor_id');
        $this->rules['number'] = "required|unique:buildings_floors_rooms,number,NULL,id,building_floor_id,$floor_id";
        $validator = Validator::make($request->all(), $this->rules, $this->rules_messages());
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }

        try {
            $BuildingFloorRoom = new BuildingFloorRoom;
            $BuildingFloorRoom->number = $request->input('number');
            $BuildingFloorRoom->available_of_accommodation = $request->input('available_of_accommodation');
            $BuildingFloorRoom->building_floor_id = $request->input('floor_id');
            $BuildingFloorRoom->save();
            return _json('success', _lang('app.added_successfully'));
        } catch (\Exception $ex) {
            return _json('error', $ex->getMessage(), 400);
        }
    }

    public function show($id) {
        $find = BuildingFloorRoom::find($id);

        if ($find) {
            return _json('success', $find);
        } else {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
    }

    public function update(Request $request, $id) {
        $BuildingFloorRoom = BuildingFloorRoom::find($id);
        if (!$BuildingFloorRoom) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
         $floor_id = $request->input('floor_id');
        $this->rules['number'] = "required|unique:buildings_floors_rooms,number,$id,id,building_floor_id,$floor_id";
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }
        try {
            $BuildingFloorRoom->number = $request->input('number');
            $BuildingFloorRoom->available_of_accommodation = $request->input('available_of_accommodation');
            $BuildingFloorRoom->save();
            return _json('success', _lang('app.updated_successfully'));
        } catch (\Exception $ex) {
            return _json('error', $ex->getMessage() . $ex->getLine(), 400);
        }
    }

    public function destroy($id) {
        $BuildingFloorRoom = BuildingFloorRoom::find($id);
        if (!$BuildingFloorRoom) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        try {
            $BuildingFloorRoom->delete();
            return _json('success', _lang('app.deleted_successfully'));
        } catch (\Exception $ex) {
            if ($ex->getCode() == 23000) {
                return _json('error', _lang('app.this_record_can_not_be_deleted_for_linking_to_other_records'), 400);
            } else {
                return _json('error', _lang('app.error_is_occured'), 400);
            }
        }
    }

    public function data(Request $request) {
        //dd( $request->input('floor'));
        $question = BuildingFloorRoom::where('building_floor_id', $request->input('floor'))
                ->select([
            'id', 'number', "available_of_accommodation",DB::RAW('(available_of_accommodation-remaining_available_of_accommodation) as remaining')
        ]);

        return \Datatables::eloquent($question)
                        ->addColumn('options', function ($item) {

                            $back = "";
                            if (\Permissions::check('buildings_floors_rooms', 'edit') || \Permissions::check('buildings_floors_rooms', 'delete')) {
                                $back .= '<div class="btn-group">';
                                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> options';
                                $back .= '<i class="fa fa-angle-down"></i>';
                                $back .= '</button>';
                                $back .= '<ul class = "dropdown-menu" role = "menu">';
                                if (\Permissions::check('buildings_floors_rooms', 'edit')) {
                                    $back .= '<li>';
                                    $back .= '<a onclick = "BuildingsFloorsRooms.edit(this);return false;" data-id = "' . $item->id . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                if (\Permissions::check('buildings_floors_rooms', 'delete')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" data-toggle="confirmation" onclick = "BuildingsFloorsRooms.delete(this);return false;" data-id = "' . $item->id . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.delete');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }


                                $back .= '</ul>';
                                $back .= ' </div>';
                            }
                            return $back;
                        })
                        ->addColumn('gender', function ($item) {
                            if ($item->gender == 1) {
                                $message = _lang('app.male');
                                $class = 'label-success';
                            } else {
                                $message = _lang('app.female');
                                $class = 'label-danger';
                            }
                            $back = '<span class="label label-sm ' . $class . '">' . $message . '</span>';
                            return $back;
                        })
                        ->filterColumn('gender', function($query, $keyword) {
                            if ($keyword == 'male') {
                                $query->where("gender", 1);
                            } else {
                                $query->where("gender", 2);
                            }
                        })
                        ->rawColumns(['options', 'gender'])
                        ->make(true);
    }

    private function rules_messages() {
        return [
            'required' => _lang('app.this_field_is_required'),
            'unique' => _lang('app.this_field_is_taken'),
        ];
    }

}
