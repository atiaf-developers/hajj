<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\PilgrimsBus;
use App\Models\User;
use App\Models\Supervisor;
use App\Models\Location;
use Validator;
use DB;

class PilgrimsBusesController extends BackendController {

    private $rules = array(
        'location' => 'required',
        'this_order' => 'required',
        'bus_number' => 'required|unique:pilgrims_buses',
        'num_of_seats' => 'required',
        'supervisor_username' => 'required|unique:users,username',
        'supervisor_password' => 'required',
        'supervisor_name' => 'required',
        'supervisor_contact_numbers' => 'required',
        'supervisor_image' => 'image|mimes:gif,png,jpeg|max:1000',
        'active' => 'required'
    );

    public function __construct() {

        parent::__construct();
        $this->middleware('CheckPermission:pilgrims_buses,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:pilgrims_buses,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:pilgrims_buses,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:pilgrims_buses,delete', ['only' => ['delete']]);
    }

    public function index(Request $request) {
        return $this->_view('pilgrims_buses/index', 'backend');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        $this->data['locations'] = Location::getAll();
        return $this->_view('pilgrims_buses/create', 'backend');
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
        }
        DB::beginTransaction();
        try {
            $supervisor = new Supervisor;

            $supervisor->name = $request->input('supervisor_name');
            $supervisor->contact_numbers = $request->input('supervisor_contact_numbers');
            if ($request->file('supervisor_image')) {

                $supervisor->supervisor_image = Supervisor::upload($request->file('supervisor_image'), 'supervisors', true);
            }else{
                $supervisor->supervisor_image ='default.png';
            }

            $supervisor->save();

            $user = new User;
            $user->username = $request->input('supervisor_username');
            $user->password = bcrypt($request->input('supervisor_password'));
            $user->type = 2;
            $user->active = $request->input('active');
            $user->save();

            $pilgrims_bus = new PilgrimsBus;
            $pilgrims_bus->location_id = $request->input('location');
            $pilgrims_bus->this_order = $request->input('this_order');
            $pilgrims_bus->bus_number = $request->input('bus_number');
            $pilgrims_bus->num_of_seats = $request->input('num_of_seats');
            $pilgrims_bus->user_id = $user->id;
            $pilgrims_bus->supervisor_id = $supervisor->id;
            $pilgrims_bus->save();

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
        $find = Location::find($id);

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
        $pilgrims_bus = PilgrimsBus::Join('supervisors', 'pilgrims_buses.supervisor_id', '=', 'supervisors.id')
                ->join('users', 'pilgrims_buses.user_id', '=', 'users.id')
                ->where('pilgrims_buses.id', $id)
                ->select('pilgrims_buses.id', 'pilgrims_buses.location_id', 'pilgrims_buses.this_order', 'pilgrims_buses.bus_number', 'pilgrims_buses.num_of_seats', 'users.username', 'users.active', 'supervisors.name', 'supervisors.contact_numbers', 'supervisors.supervisor_image')
                ->first();

        if (!$pilgrims_bus) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        $this->data['locations'] = Location::getAll();
        $this->data['pilgrims_bus'] = $pilgrims_bus;

        return $this->_view('pilgrims_buses/edit', 'backend');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {


        $pilgrims_bus = PilgrimsBus::find($id);
        if (!$pilgrims_bus) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        unset($this->rules['supervisor_image'], $this->rules['supervisor_password']);

        $this->rules['bus_number'] = 'required|unique:pilgrims_buses,bus_number,' . $id;
        $this->rules['supervisor_username'] = 'required|unique:users,username,' . $pilgrims_bus->user_id;

        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }


        DB::beginTransaction();
        try {
            $pilgrims_bus->location_id = $request->input('location');
            $pilgrims_bus->this_order = $request->input('this_order');
            $pilgrims_bus->bus_number = $request->input('bus_number');
            $pilgrims_bus->num_of_seats = $request->input('num_of_seats');
            $pilgrims_bus->save();

            $supervisor = Supervisor::where('id', $pilgrims_bus->supervisor_id)->first();

            $user = User::where('id', $pilgrims_bus->user_id)->first();


            $supervisor->name = $request->input('supervisor_name');
            $supervisor->contact_numbers = $request->input('supervisor_contact_numbers');

            if ($request->file('supervisor_image')) {
                $old_image = $supervisor->supervisor_image;
                Supervisor::deleteUploaded('supervisors', $old_image);
                $supervisor->supervisor_image = Supervisor::upload($request->file('supervisor_image'), 'supervisors', true);
            }
            $supervisor->save();


            $user->username = $request->input('supervisor_username');
            if ($request->input('supervisor_password')) {
                $user->password = bcrypt($request->input('supervisor_password'));
            }
            $user->active = $request->input('active');
            $user->save();

            DB::commit();
            return _json('success', _lang('app.updated_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            dd($ex);
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
        $pilgrims_bus = PilgrimsBus::find($id);
        if (!$pilgrims_bus) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        DB::beginTransaction();
        try {
            $pilgrims_bus->delete();
            DB::commit();
            return _json('success', _lang('app.deleted_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            if ($ex->getCode() == 23000) {
                return _json('error', _lang('app.this_record_can_not_be_deleted_for_linking_to_other_records'), 400);
            } else {
                return _json('error', _lang('app.error_is_occured'), 400);
            }
        }
    }

    public function data(Request $request) {

        $pilgrims_buses = PilgrimsBus::Join('supervisors', 'pilgrims_buses.supervisor_id', '=', 'supervisors.id')
                ->join('users', 'pilgrims_buses.user_id', '=', 'users.id')
                ->select(['pilgrims_buses.id', 'pilgrims_buses.bus_number', 'pilgrims_buses.num_of_seats', 'supervisors.name', 'supervisors.supervisor_image']);

        return \Datatables::eloquent($pilgrims_buses)
                        ->addColumn('options', function ($item) {

                            $back = "";
                            if (\Permissions::check('pilgrims_buses', 'edit') || \Permissions::check('pilgrims_buses', 'delete')) {
                                $back .= '<div class="btn-group">';
                                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> options';
                                $back .= '<i class="fa fa-angle-down"></i>';
                                $back .= '</button>';
                                $back .= '<ul class = "dropdown-menu" role = "menu">';
                                if (\Permissions::check('pilgrims_buses', 'edit')) {
                                    $back .= '<li>';
                                    $back .= '<a href="' . route('pilgrims_buses.edit', $item->id) . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                if (\Permissions::check('pilgrims_buses', 'delete')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" data-toggle="confirmation" onclick = "PilgrimsBuses.delete(this);return false;" data-id = "' . $item->id . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.delete');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                $back .= '</ul>';
                                $back .= ' </div>';
                            }
                            return $back;
                        })
                        ->editColumn('supervisor_image', function ($item) {

                            $back = '<img src="' . url('public/uploads/supervisors/' . $item->supervisor_image) . '" style="height:64px;width:64px;"/>';

                            return $back;
                        })
                        /* ->addColumn('active', function ($item) {
                          if ($item->active == 1) {
                          $message = _lang('app.active');
                          $class = 'label-success';
                          } else {
                          $message = _lang('app.not_active');
                          $class = 'label-danger';
                          }
                          $back = '<span class="label label-sm ' . $class . '">' . $message . '</span>';
                          return $back;
                          }) */
                        ->escapeColumns([])
                        ->make(true);
    }

}
