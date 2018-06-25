<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\Building;
use App\Models\Lounge;
use Validator;
use DB;

class BuildingsController extends BackendController {

    private $rules = array(
        'number' => 'required|unique:buildings,number',
        'this_order' => 'required',
    );

    public function __construct() {
        parent::__construct();
        $this->middleware('CheckPermission:buildings,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:buildings,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:buildings,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:buildings,delete', ['only' => ['delete']]);
    }

    public function index() {

        return $this->_view('buildings/index', 'backend');
    }

    public function create() {
        return $this->_view('buildings/create', 'backend');
    }

    public function store(Request $request) {

        $validator = Validator::make($request->all(), $this->rules, $this->rules_messages());
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }

        try {
            $Building = new Building;
            $Building->number = $request->input('number');
            $Building->this_order = $request->input('this_order');
            $Building->save();
            return _json('success', _lang('app.added_successfully'));
        } catch (\Exception $ex) {
            return _json('error', $ex->getMessage(), 400);
        }
    }
      public function show($id) {
        $find = Building::find($id);

        if ($find) {
            return _json('success', $find);
        } else {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
    }

    public function edit($id) {
        $find = Building::find($id);
        if (!$find) {
            return $this->err404();
        }

        $this->data['suite'] = $find;

        return $this->_view('buildings/edit', 'backend');
    }

    public function update(Request $request, $id) {
        $Building = Building::find($id);
        if (!$Building) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        $this->rules['number'] = 'required|unique:buildings,number,' . $id;
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }
        try {
            $Building->number = $request->input('number');
            $Building->this_order = $request->input('this_order');
            $Building->save();
            return _json('success', _lang('app.updated_successfully'));
        } catch (\Exception $ex) {
            return _json('error', $ex->getMessage() . $ex->getLine(), 400);
        }
    }

    public function destroy($id) {
        $Building = Building::find($id);
        if (!$Building) {
           return _json('error', _lang('app.error_is_occured'), 404);
        }
        try {
            $Building->delete();
            return _json('success', _lang('app.deleted_successfully'));
        } catch (\Exception $ex) {
            if ($ex->getCode() == 23000) {
                return _json('error',  _lang('app.this_record_can_not_be_deleted_for_linking_to_other_records'), 400);
            } else {
                return _json('error', _lang('app.error_is_occured'), 400);
            }
        }
    }

    public function data(Request $request) {
        $question = Building::select([
                    'id', 'number', "this_order"
        ]);

        return \Datatables::eloquent($question)
                        ->addColumn('options', function ($item) {

                            $back = "";
                            if (\Permissions::check('buildings', 'edit') || \Permissions::check('buildings', 'delete')) {
                                $back .= '<div class="btn-group">';
                                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> options';
                                $back .= '<i class="fa fa-angle-down"></i>';
                                $back .= '</button>';
                                $back .= '<ul class = "dropdown-menu" role = "menu">';
                                if (\Permissions::check('buildings', 'edit')) {
                                    $back .= '<li>';
                                    $back .= '<a onclick = "Buildings.edit(this);return false;" data-id = "' . $item->id . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                if (\Permissions::check('buildings', 'delete')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" data-toggle="confirmation" onclick = "Buildings.delete(this);return false;" data-id = "' . $item->id . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.delete');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }
                                if (\Permissions::check('buildings_floors', 'open')) {
                                    $back .= '<li>';
                                     $back .= '<a href="' . url('admin/buildings_floors?building='. $item->id) . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.floors');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                $back .= '</ul>';
                                $back .= ' </div>';
                            }
                            return $back;
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
