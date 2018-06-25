<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\PilgrimClass;
use App\Models\PilgrimClassTranslation;
use App\Models\Supervisor;
use Validator;
use DB;

class PilgrimsClassController extends BackendController {

    private $rules = array(
        'this_order' => 'required',
        'supervisor_name' => 'required',
        'supervisor_contact_numbers' => 'required',
        'supervisor_image' => 'required|image|mimes:gif,png,jpeg|max:1000'
    );

    public function __construct() {

        parent::__construct();
        $this->middleware('CheckPermission:pilgrims_class,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:pilgrims_class,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:pilgrims_class,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:pilgrims_class,delete', ['only' => ['delete']]);
    }

    public function index(Request $request) {
        return $this->_view('pilgrims_class/index', 'backend');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        return $this->_view('pilgrims_class/create', 'backend');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $columns_arr = array(
            'title' => 'required|unique:pilgrims_class_translations,title'
        );
        $lang_rules = $this->lang_rules($columns_arr);
        $this->rules = array_merge($this->rules, $lang_rules);
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }
        DB::beginTransaction();
        try {
            $supervisor = new Supervisor;

            $supervisor->name = $request->input('supervisor_name');
            $supervisor->contact_numbers = $request->input('supervisor_contact_numbers');
            if ($request->file('supervisor_image')) {
                $supervisor->supervisor_image = Supervisor::upload($request->file('supervisor_image'), 'supervisors', true);
            } else {
                $supervisor->supervisor_image = 'default.png';
            }


            $supervisor->save();

            $pilgrim_class = new PilgrimClass;
            $pilgrim_class->this_order = $request->input('this_order');
            $pilgrim_class->supervisor_id = $supervisor->id;
            $pilgrim_class->save();

            $pilgrim_class_translations = array();

            foreach ($request->input('title') as $key => $value) {
                $pilgrim_class_translations[] = array(
                    'locale' => $key,
                    'title' => $value,
                    'pilgrims_class_id' => $pilgrim_class->id
                );
            }

            PilgrimClassTranslation::insert($pilgrim_class_translations);

            DB::commit();
            return _json('success', _lang('app.added_successfully'));
        } catch (\Exception $ex) {
            dd($ex);
            DB::rollback();
            return _json('error', _lang('app.error_is_occured'), 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $find = PilgrimClass::find($id);

        if ($find) {
            return _json('success', $find);
        } else {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $pilgrim_class = PilgrimClass::Join('supervisors', 'pilgrims_class.supervisor_id', '=', 'supervisors.id')
                ->where('pilgrims_class.id', $id)
                ->select('pilgrims_class.id', 'pilgrims_class.this_order', 'supervisors.name', 'supervisors.contact_numbers', 'supervisors.supervisor_image')
                ->first();

        if (!$pilgrim_class) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        $this->data['pilgrim_class_translations'] = PilgrimClassTranslation::where('pilgrims_class_id', $id)->pluck('title', 'locale');

        $this->data['pilgrim_class'] = $pilgrim_class;

        return $this->_view('pilgrims_class/edit', 'backend');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $pilgrim_class = PilgrimClass::find($id);
        if (!$pilgrim_class) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        unset($this->rules['supervisor_image']);
        $columns_arr = array(
            'title' => 'required|unique:pilgrims_class_translations,title,' . $id . ',pilgrims_class_id'
        );

        $lang_rules = $this->lang_rules($columns_arr);
        $this->rules = array_merge($this->rules, $lang_rules);
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }


        DB::beginTransaction();
        try {

            $pilgrim_class->this_order = $request->input('this_order');
            $pilgrim_class->save();

            $supervisor = Supervisor::where('id', $pilgrim_class->supervisor_id)->first();

            $supervisor->name = $request->input('supervisor_name');
            $supervisor->contact_numbers = $request->input('supervisor_contact_numbers');

            if ($request->file('supervisor_image')) {
                $old_image = $supervisor->supervisor_image;
                Supervisor::deleteUploaded('supervisors', $old_image, '\App\Models\Supervisor');
                $supervisor->supervisor_image = Supervisor::upload($request->file('supervisor_image'), 'supervisors', true);
            }
            $supervisor->save();

            PilgrimClassTranslation::where('pilgrims_class_id', $pilgrim_class->id)->delete();

            $pilgrim_class_translations = array();
            foreach ($request->input('title') as $key => $value) {
                $pilgrim_class_translations[] = array(
                    'locale' => $key,
                    'title' => $value,
                    'pilgrims_class_id' => $pilgrim_class->id
                );
            }
            PilgrimClassTranslation::insert($pilgrim_class_translations);
            DB::commit();
            return _json('success', _lang('app.updated_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            return _json('error', _lang('app.error_is_occured'), 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $pilgrim_class = PilgrimClass::find($id);
        if (!$pilgrim_class) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        DB::beginTransaction();
        try {
            $pilgrim_class->delete();
            DB::commit();
            return _json('success', _lang('app.deleted_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            if ($ex->getCode() == 23000) {
                return _json('error', $ex->getMessage(), 400);
            } else {
                return _json('error', _lang('app.error_is_occured'), 400);
            }
        }
    }

    public function data(Request $request) {

        $pilgrims_class = PilgrimClass::Join('pilgrims_class_translations', 'pilgrims_class.id', '=', 'pilgrims_class_translations.pilgrims_class_id')
                ->where('pilgrims_class_translations.locale', $this->lang_code)
                ->select([
            'pilgrims_class.id', "pilgrims_class_translations.title", "pilgrims_class.this_order"
        ]);

        return \Datatables::eloquent($pilgrims_class)
                        ->addColumn('options', function ($item) {

                            $back = "";
                            if (\Permissions::check('pilgrims_class', 'edit') || \Permissions::check('pilgrims_class', 'delete')) {
                                $back .= '<div class="btn-group">';
                                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> options';
                                $back .= '<i class="fa fa-angle-down"></i>';
                                $back .= '</button>';
                                $back .= '<ul class = "dropdown-menu" role = "menu">';
                                if (\Permissions::check('pilgrims_class', 'edit')) {
                                    $back .= '<li>';
                                    $back .= '<a href="' . route('pilgrims_class.edit', $item->id) . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                if (\Permissions::check('pilgrims_class', 'delete')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" data-toggle="confirmation" onclick = "PilgrimsClass.delete(this);return false;" data-id = "' . $item->id . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.delete');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                $back .= '</ul>';
                                $back .= ' </div>';
                            }
                            return $back;
                        })
                        ->escapeColumns([])
                        ->make(true);
    }

}
