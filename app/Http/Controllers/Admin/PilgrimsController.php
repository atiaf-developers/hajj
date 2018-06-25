<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\Pilgrim;
use App\Models\Location;
use App\Models\PilgrimClass;
use App\Traits\HajjTrait;
use Validator;
use DB;
use QrCode;

class PilgrimsController extends BackendController {

    use HajjTrait;

    private $rules = array(
        'name' => 'required',
        'nationality' => 'required',
        'mobile' => 'required',
        'ssn' => 'required|unique:pilgrims,ssn',
        'reservation_no' => 'required',
        'location' => 'required',
        'pilgrim_class' => 'required',
        'gender' => 'required'
    );
    private $import_rules = array(
        'file' => 'required|mimes:csv,txt',
        'location' => 'required',
        'pilgrim_class' => 'required',
        'gender' => 'required',
    );

    public function __construct() {
        parent::__construct();
        $this->middleware('CheckPermission:pilgrims,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:pilgrims,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:pilgrims,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:pilgrims,delete', ['only' => ['delete']]);
    }

    public function index() {

        $this->data['locations'] = Location::getAll();
        $this->data['pilgrims_class'] = PilgrimClass::getAll();
        return $this->_view('pilgrims/index', 'backend');
    }

    public function import(Request $request) {

        $validator = Validator::make($request->all(), $this->import_rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        } else {
            //dd('here');
            $extra_params = array(
                'location' => $request->input('location'),
                'pilgrim_class' => $request->input('pilgrim_class'),
                'gender' => $request->input('gender'),
            );
            $csv = Pilgrim::upload_simple($request->file('file'), 'csv');
            $path = public_path('uploads/csv/' . $csv);
            try {
                $this->_import_csv_hajj('pilgrims', $path, $extra_params);
                return _json('success', date('Y-m-d H-i-s'));
            } catch (\Exception $ex) {
                return _json('error', $ex->getMessage() . $ex->getFile(), 400);
            }
        }
    }

    public function generateQr(Request $request) {
        $created_at = $request->input('created_at');
        $pilgrims = Pilgrim::where('created_at', $created_at)->get();
        //dd($pilgrims);
        if ($pilgrims->count() > 0) {
            $data_update = [];
            foreach ($pilgrims as $one) {
                $qr_image_name = time() . mt_rand(1, 1000000) . '.png';
                QrCode::format('png')->size(300)->generate($one->code, base_path('public/uploads/pilgrims/' . $qr_image_name));
                $data_update['qr_image'][] = [
                    'id' => $one->id,
                    'value' => "'$qr_image_name'"
                ];
            }
            //dd($data_update);
            $this->updateValues('App\Models\Pilgrim', $data_update);
        }

        return _json('success', _lang('app.proccess_done_successfully'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $this->data['measurements'] = $this->geBodyMeasurements();
        return $this->_view('pilgrims/create', 'backend');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        } else {
            $lastPilgrim = Pilgrim::orderBy('id', 'DESC')->first();
            $location = Location::where('id', $request->input('location'))->first();
            $lastPilgrimId = $lastPilgrim ? $lastPilgrim->id++ : 1;
            try {
                $Pilgrim = new Pilgrim;
                $Pilgrim->name = $request->input('name');
                $Pilgrim->nationality = $request->input('nationality');
                $Pilgrim->mobile = $request->input('mobile');
                $Pilgrim->ssn = $request->input('ssn');
                $Pilgrim->reservation_no = $request->input('reservation_no');
                $Pilgrim->location_id = $request->input('location');
                $Pilgrim->pilgrim_class_id = $request->input('pilgrim_class');
                $Pilgrim->gender = $request->input('gender');
                $Pilgrim->code = $this->getNextSerialNumber($lastPilgrimId, $location->prefix);
                if ($request->file('image')) {
                    $Pilgrim->image = Pilgrim::upload($request->file('image'), 'pilgrims', true);
                } else {
                    $Pilgrim->image = $Pilgrim->gender ? 'male.png' : 'female.png';
                }
                $qr_image_name = time() . mt_rand(1, 1000000) . '.png';
                QrCode::format('png')->size(300)->generate($Pilgrim->code, base_path('public/uploads/pilgrims/' . $qr_image_name));
                $Pilgrim->qr_image = $qr_image_name;
                $Pilgrim->save();
                return _json('success', _lang('app.added_successfully'));
            } catch (\Exception $ex) {
                return _json('error', _lang('app.error_is_occured'), 400);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $find = Pilgrim::find($id);

        if ($find) {
            return _json('success', $find);
        } else {
            return _json('success', 'error');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $find = Pilgrim::find($id);
        if (!$find) {
            return $this->err404();
        }
        //dd($this->data['sizes']);
        $this->data['category'] = $find;
        $this->data['measurements'] = $this->geBodyMeasurements();
        $this->data['measurements_selected'] = PilgrimBodyMeasurement::where('category_id', $find->id)->pluck('body_measurement_id')->toArray();
        return $this->_view('pilgrims/edit', 'backend');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        $Pilgrim = Pilgrim::find($id);
        if (!$Pilgrim) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        $this->rules['ssn'] = 'required|unique:pilgrims,ssn,' . $id;
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        } else {

            try {
                $Pilgrim->name = $request->input('name');
                $Pilgrim->nationality = $request->input('nationality');
                $Pilgrim->mobile = $request->input('mobile');
                $Pilgrim->ssn = $request->input('ssn');
                $Pilgrim->reservation_no = $request->input('reservation_no');
                $Pilgrim->location_id = $request->input('location');
                $Pilgrim->pilgrim_class_id = $request->input('pilgrim_class');
                $Pilgrim->gender = $request->input('gender');
                if ($request->file('image')) {
                    $Pilgrim->image = Pilgrim::upload($request->file('image'), 'pilgrims', true);
                }

                $Pilgrim->save();

                return _json('success', _lang('app.updated_successfully'));
            } catch (\Exception $ex) {

                return _json('error', _lang('app.error_is_occured'), 400);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $Pilgrim = Pilgrim::find($id);
        if (!$Pilgrim) {
            return _json('error', _lang('app.error_is_occured'), 400);
        }
        try {
            $Pilgrim->delete();
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
        $pilgrims = Pilgrim::join('locations', 'pilgrims.location_id', '=', 'locations.id');
        $pilgrims->join('locations_translations as trans', 'locations.id', '=', 'trans.location_id');
        //suites accommodation
        $pilgrims->leftJoin('suites_accommodation', 'pilgrims.id', '=', 'suites_accommodation.pilgrim_id');
        //buildings accommodation
        $pilgrims->leftJoin('buildings_accommodation', 'pilgrims.id', '=', 'buildings_accommodation.pilgrim_id');

        //tents accommodation
        $pilgrims->leftJoin('tents_accommodation', 'pilgrims.id', '=', 'tents_accommodation.pilgrim_id');
        $pilgrims->where('trans.locale', $this->lang_code);
        $pilgrims->select([
            'pilgrims.id', "pilgrims.ssn", "pilgrims.name", "pilgrims.active", "pilgrims.reservation_no", "pilgrims.code", "trans.title",
            "suites_accommodation.id as suite_accommodation_id", "buildings_accommodation.id as building_accommodation_id", "tents_accommodation.id as tent_accommodation_id"
        ]);

        return \Datatables::eloquent($pilgrims)
                        ->addColumn('options', function ($item) {

                            $back = "";
                            if (\Permissions::check('pilgrims', 'edit') || \Permissions::check('pilgrims', 'delete')) {
                                $back .= '<div class="btn-group">';
                                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> options';
                                $back .= '<i class="fa fa-angle-down"></i>';
                                $back .= '</button>';
                                $back .= '<ul class = "dropdown-menu" role = "menu">';

                                if (\Permissions::check('pilgrims', 'edit')) {
                                    $back .= '<li>';
                                    $back .= '<a  onclick = "Pilgrims.edit(this);return false;" data-id = "' . $item->id . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                if (\Permissions::check('pilgrims', 'delete')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" data-toggle="confirmation" onclick = "Pilgrims.delete(this);return false;" data-id = "' . $item->id . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.delete');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }
                                $back .= '</ul>';
                                $back .= ' </div>';
                            }
                            return $back;
                        })
                        ->addColumn('active', function ($item) {
                            if ($item->active == 1) {
                                $message = _lang('app.active');
                                $class = 'label-success';
                            } else {
                                $message = _lang('app.not_active');
                                $class = 'label-danger';
                            }
                            $back = '<span class="label label-sm ' . $class . '">' . $message . '</span>';
                            return $back;
                        })
                        ->addColumn('accommodation_status', function ($item) {
                            if ($item->suite_accommodation_id || $item->building_accommodation_id || $item->tent_accommodation_id) {
                                $message = _lang('app.has_been_accommodation');
                                $class = 'label-success';
                            } else {
                                $message = _lang('app.has_not_been_accommodation');
                                $class = 'label-danger';
                            }
                            $back = '<span class="label label-sm ' . $class . '">' . $message . '</span>';
                            return $back;
                        })
                        ->escapeColumns([])
                        ->make(true);
    }

}
