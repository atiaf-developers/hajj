<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\OurLocation;
use App\Models\OurLocationTranslation;
use Validator;
use DB;

class OurLocationsController extends BackendController {

    private $rules = array(
        'location_image'    => 'required|image|mimes:gif,png,jpeg|max:1000',
        'contact_numbers' => 'required',
        'this_order' => 'required',
        'active' => 'required',
        'lat' => 'required',
        'lng' => 'required'    
    );

    public function __construct() {
        parent::__construct();
        $this->middleware('CheckPermission:our_locations,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:our_locations,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:our_locations,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:our_locations,delete', ['only' => ['delete']]);
    }

    public function index() {
        return $this->_view('our_locations/index', 'backend');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return $this->_view('our_locations/create', 'backend');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

         $columns_arr = array(
            'title' => 'required|unique:our_locations_translations,title'
        );
        $lang_rules = $this->lang_rules($columns_arr);
        $this->rules = array_merge($this->rules,$lang_rules);

        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        } 
        DB::beginTransaction();
        try {
                $location = new OurLocation();
                $location->contact_numbers = $request->input('contact_numbers');
                $location->lat = $request->input('lat');
                $location->lng = $request->input('lng');
                $location->this_order = $request->input('this_order');
                $location->active = $request->input('active');
                $location->location_image = OurLocation::upload($request->file('location_image'), 'our_locations', true);
                $location->save();

                $location_translations = array();
                foreach ($request->input('title') as $key => $value) {
                 $location_translations[] = array(
                        'locale' => $key,
                        'title' => $value,
                        'address' => getAddress($request->input('lat'), $request->input('lng'), $lang = $key),
                        'our_location_id' => $location->id
                    );
            }

            OurLocationTranslation::insert($location_translations);

            DB::commit();

                return _json('success', _lang('app.added_successfully'));
            } catch (\Exception $ex) {
                DB::rollback();
                return _json('error', _lang('app.error_is_occured'), 400);
            }
        
    }

  

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $location = OurLocation::find($id);
        
        if (!$location) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        
         $this->data['location_translations'] = OurLocationTranslation::where('our_location_id',$id)->pluck('title','locale');
   
        $this->data['location'] = $location;
        return $this->_view('our_locations/edit', 'backend');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        $location = OurLocation::find($id);
        if (!$location) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        unset($this->rules['location_image']);
        $columns_arr = array(
            'title' => 'required|unique:our_locations_translations,title,'.$id.',our_location_id'
        );

        $lang_rules = $this->lang_rules($columns_arr);
        $this->rules = array_merge($this->rules,$lang_rules);
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        } 


         DB::beginTransaction();
            try {

                $location->contact_numbers = $request->input('contact_numbers');
                $location->lat = $request->input('lat');
                $location->lng = $request->input('lng');
                $location->this_order = $request->input('this_order');
                $location->active = $request->input('active');

                if ($request->file('location_image')) {
                $old_image = $location->location_image;
                OurLocation::deleteUploaded('our_locations', $old_image);
                $location->location_image = OurLocation::upload($request->file('location_image'), 'our_locations', true);
                }
                $location->save();
                $location_translations = array();

                OurLocationTranslation::where('our_location_id', $location->id)->delete();

                foreach ($request->input('title') as $key => $value) {
                 $location_translations[] = array(
                        'locale' => $key,
                        'title' => $value,
                        'address' => getAddress($request->input('lat'), $request->input('lng'), $lang = $key),
                        'our_location_id' => $location->id
                    );
            }
            
            OurLocationTranslation::insert($location_translations);
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
        $location = OurLocation::find($id);
        if (!$location) {
            return _json('error', _lang('app.error_is_occured'), 400);
        }
        DB::beginTransaction();
        try {
            $old_image = $location->location_image;
            OurLocationTranslation::where('our_location_id', $location->id)->delete();
            $location->delete();
            OurLocation::deleteUploaded('our_locations', $old_image);
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

        $locations = OurLocation::Join('our_locations_translations','our_locations.id','=','our_locations_translations.our_location_id')
        ->where('our_locations_translations.locale', $this->lang_code)
        ->select([
            'our_locations.id', "our_locations_translations.title", "our_locations.this_order",'our_locations.active','our_locations.location_image'
        ]);

        return \Datatables::eloquent($locations)
                        ->addColumn('options', function ($item) {

                            $back = "";
                            if (\Permissions::check('our_locations', 'edit') || \Permissions::check('our_locations', 'delete')) {
                                $back .= '<div class="btn-group">';
                                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> options';
                                $back .= '<i class="fa fa-angle-down"></i>';
                                $back .= '</button>';
                                $back .= '<ul class = "dropdown-menu" role = "menu">';

                                if (\Permissions::check('our_locations', 'edit')) {
                                    $back .= '<li>';
                                    $back .= '<a href="'.route('our_locations.edit',$item->id).'">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                if (\Permissions::check('our_locations', 'delete')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" data-toggle="confirmation" onclick = "OurLocations.delete(this);return false;" data-id = "' . $item->id . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.delete');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }
                                $back .= '</ul>';
                                $back .= ' </div>';
                            }
                            return $back;
                        })
                        ->editColumn('location_image', function ($item) {
                            $back = '<img src="' . url('public/uploads/our_locations/' . $item->location_image) . '" style="height:64px;width:64px;"/>';
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
