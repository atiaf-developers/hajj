<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\User;
use Validator;

class ManagersController extends BackendController {

    private $rules = array(
        'username' => 'required|unique:users',
        'password' => 'required',
        'active' => 'required',
    );

    public function __construct() {

        parent::__construct();
        $this->middleware('CheckPermission:managers,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:managers,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:managers,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:managers,delete', ['only' => ['delete']]);
    }

    public function index() {
        return $this->_view('managers/index', 'backend');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
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
        try {

             $manager = new User;
             $manager->username = $request->input('username');
             $manager->password = bcrypt($request->input('password'));
             $manager->active = $request->input('active');
             $manager->type = 1;
             $manager->save();

            return _json('success', _lang('app.added_successfully'));
        } catch (\Exception $e) {
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
        $find = User::find($id);

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
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        $manager = User::find($id);
        if (!$manager) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }

        unset($this->rules['password']);
        $this->rules['username'] = 'required|unique:users,username,'. $id;
       $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }

        try {
            $manager->username = $request->input('username');
            if ($request->input('password')) {
                $manager->password = bcrypt($request->input('password'));
            }
            $manager->active = $request->input('active');
            $manager->save();
           return _json('success', _lang('app.updated_successfully'));
        } catch (\Exception $e) {
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
        $manager = User::find($id);
        if (!$manager) {
           return _json('error', _lang('app.error_is_occured'), 404);
        }
        try {
            $manager->delete();
            return _json('success', _lang('app.deleted_successfully'));
        } catch (\Exception $ex) {
            if ($ex->getCode() == 23000) {
                return _json('error', $ex->getMessage(), 400);
            } else {
                return _json('error', _lang('app.error_is_occured'), 400);
            }
        }
    }

    public function data() {

        $admin = User::where('type', 1)->select('users.*');

        return \Datatables::eloquent($admin)
                        ->addColumn('options', function ($item) {

                            $back = "";
                            if (\Permissions::check('managers', 'edit') || \Permissions::check('managers', 'delete')) {
                                $back .= '<div class="btn-group">';
                                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> options';
                                $back .= '<i class="fa fa-angle-down"></i>';
                                $back .= '</button>';
                                $back .= '<ul class = "dropdown-menu" role = "menu">';
                                if (\Permissions::check('managers', 'edit')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" onclick = "Managers.edit(this);return false;" data-id = "' . $item->id . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }


                                if (\Permissions::check('managers', 'delete')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" data-toggle="confirmation" onclick = "Managers.delete(this);return false;" data-id = "' . $item->id . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.delete');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }


                                $back .= '</ul>';
                                $back .= ' </div>';
                            }
                            return $back;
                        })

                        ->editColumn('active', function ($item) {
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
                        ->escapeColumns([])
                        ->make(true);
    }

}
