<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\Supervisor;
use App\Models\CommunicationGuide;
use App\Models\CommunicationGuideSupervisor;
use App\Models\SupervisorJob;
use Validator;
use DB;

class CommunicationGuideSupervisorsController extends BackendController {

    private $rules = array(
        'name' => 'required',
        'image' => 'required|image|mimes:gif,png,jpeg|max:1000',
        'contact_numbers' => 'required',
        'job' => 'required',
    );

    public function __construct() {

        parent::__construct();
        $this->middleware('CheckPermission:communication_guide_supervisors,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:communication_guide_supervisors,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:communication_guide_supervisors,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:communication_guide_supervisors,delete', ['only' => ['delete']]);
    }

    public function index(Request $request) {

         $this->data['communication_guide'] = CommunicationGuide::Join('communication_guides_translations', 'communication_guides.id', '=', 'communication_guides_translations.communication_guide_id')
                ->where('communication_guides_translations.locale', $this->lang_code)
                ->where('communication_guides.id',$request->input('communication_guide'))
                ->select('communication_guides.id','communication_guides_translations.title')
                ->first();


        $this->data['supervisors_jobs'] = SupervisorJob::Join('supervisors_jobs_translations','supervisors_jobs.id','=','supervisors_jobs_translations.supervisor_job_id')
        ->where('supervisors_jobs_translations.locale', $this->lang_code)
        ->where('supervisors_jobs.active', true)
        ->select([
            'supervisors_jobs.id', "supervisors_jobs_translations.title"
        ])
        ->get();

        return $this->_view('communication_guide_supervisors/index', 'backend');
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

            $supervisor = new Supervisor;

            $supervisor->name = $request->input('name');
            $supervisor->contact_numbers = $request->input('contact_numbers');
            if($request->file('image')){
                 $supervisor->supervisor_image = Supervisor::upload($request->file('image'), 'supervisors', true);
            }else{
                 $supervisor->supervisor_image ='default.png';
            }
           
            $supervisor->supervisor_job_id = $request->input('job');

            $supervisor->save();

            //CommunicationGuideSupervisor
            $communication_guide_supervisor = new CommunicationGuideSupervisor();
            $communication_guide_supervisor->supervisor_id = $supervisor->id;
            $communication_guide_supervisor->communication_guide_id = $request->input('communication_guide');
            $communication_guide_supervisor->save();


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
        $find = Supervisor::find($id);

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

        $supervisor =  Supervisor::find($id);
        if (!$supervisor) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        
        if (!$request->file('image')) {
            unset($this->rules['image']);
        }
       
       $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }

        try {
            $supervisor->name = $request->input('name');
            $supervisor->contact_numbers = $request->input('contact_numbers');
            $supervisor->supervisor_job_id = $request->input('job');
            if ($request->file('supervisor_image')) {
                $old_image = $supervisor->supervisor_image;
                Supervisor::deleteUploaded('supervisors', $old_image);
                $supervisor->supervisor_image = Supervisor::upload($request->file('image'), 'supervisors', true);
            }
            $supervisor->save();
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
        $supervisor = Supervisor::find($id);
        if (!$supervisor) {
           return _json('error', _lang('app.error_is_occured'), 404);
        }
        try {
            DB::table('communication_guide_supervisors')->where('supervisor_id',$id)->delete();
            $supervisor->delete();
            return _json('success', _lang('app.deleted_successfully'));
        } catch (\Exception $ex) {
            if ($ex->getCode() == 23000) {
                return _json('error', $ex->getMessage(), 400);
            } else {
                return _json('error', _lang('app.error_is_occured'), 400);
            }
        }
    }

    public function data(Request $request) {
        
        $communication_guide = $request->input('communication_guide');

        $admin = CommunicationGuide::
        Join('communication_guide_supervisors','communication_guide_supervisors.communication_guide_id','=','communication_guides.id')
        ->join('supervisors','communication_guide_supervisors.supervisor_id','=','supervisors.id')
        ->where('communication_guides.id',$communication_guide)
        ->select('supervisors.id','supervisors.name','supervisors.supervisor_image','supervisors.contact_numbers');

        return \Datatables::eloquent($admin)
                        ->addColumn('options', function ($item) {

                            $back = "";
                            if (\Permissions::check('communication_guide_supervisors', 'edit') || \Permissions::check('communication_guide_supervisors', 'delete')) {
                                $back .= '<div class="btn-group">';
                                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> options';
                                $back .= '<i class="fa fa-angle-down"></i>';
                                $back .= '</button>';
                                $back .= '<ul class = "dropdown-menu" role = "menu">';
                                if (\Permissions::check('communication_guide_supervisors', 'edit')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" onclick = "CommunicationGuideSupervisors.edit(this);return false;" data-id = "' . $item->id . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }


                                if (\Permissions::check('communication_guide_supervisors', 'delete')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" data-toggle="confirmation" onclick = "CommunicationGuideSupervisors.delete(this);return false;" data-id = "' . $item->id . '">';
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
                        ->escapeColumns([])
                        ->make(true);
    }

}
