<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\SupervisorJob;
use App\Models\SupervisorJobTranslation;
use Validator;
use DB;

class SupervisorsJobsController extends BackendController {

    private $rules = array(
       
        'this_order' => 'required',
        'active' => 'required',
       
    );

    public function __construct() {
        parent::__construct();
        $this->middleware('CheckPermission:supervisors_jobs,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:supervisors_jobs,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:supervisors_jobs,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:supervisors_jobs,delete', ['only' => ['delete']]);
    }

    public function index() {
        return $this->_view('supervisors_jobs/index', 'backend');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return $this->_view('supervisors_jobs/create', 'backend');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

         $columns_arr = array(
            'title' => 'required|unique:supervisors_jobs_translations,title'
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

                $supervisor_job = new SupervisorJob();
                $supervisor_job->this_order = $request->input('this_order');
                $supervisor_job->active = $request->input('active');

                $supervisor_job->save();

                $supervisor_job_translations = array();
                foreach ($request->input('title') as $key => $value) {
                 $supervisor_job_translations[] = array(
                        'locale' => $key,
                        'title' => $value,
                        'supervisor_job_id' => $supervisor_job->id
                    );
                }

            SupervisorJobTranslation::insert($supervisor_job_translations);

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
        $supervisor_job = SupervisorJob::find($id);
        
        if (!$supervisor_job) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        
         $this->data['supervisor_job_translations'] = SupervisorJobTranslation::where('supervisor_job_id',$id)->pluck('title','locale');
   
        $this->data['supervisor_job'] = $supervisor_job;
        return $this->_view('supervisors_jobs/edit', 'backend');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        $supervisor_job = SupervisorJob::find($id);
        if (!$supervisor_job) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
       
        $columns_arr = array(
            'title' => 'required|unique:supervisors_jobs_translations,title,'.$id.',supervisor_job_id'
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

              
                $supervisor_job->this_order = $request->input('this_order');
                $supervisor_job->active = $request->input('active');

                $supervisor_job->save();

                $supervisor_job_translations = array();
                SupervisorJobTranslation::where('supervisor_job_id', $supervisor_job->id)->delete();

                foreach ($request->input('title') as $key => $value) {
                 $supervisor_job_translations[] = array(
                        'locale' => $key,
                        'title' => $value,
                        'supervisor_job_id' => $supervisor_job->id
                    );
                }

            SupervisorJobTranslation::insert($supervisor_job_translations);
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
        $supervisor_job = SupervisorJob::find($id);
        if (!$supervisor_job) {
            return _json('error', _lang('app.error_is_occured'), 400);
        }
        DB::beginTransaction();
        try {

            $supervisor_job->delete();
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

        $supervisors_jobs = SupervisorJob::Join('supervisors_jobs_translations','supervisors_jobs.id','=','supervisors_jobs_translations.supervisor_job_id')
        ->where('supervisors_jobs_translations.locale', $this->lang_code)
        ->select([
            'supervisors_jobs.id', "supervisors_jobs_translations.title", "supervisors_jobs.this_order",'supervisors_jobs.active'
        ]);

        return \Datatables::eloquent($supervisors_jobs)
                        ->addColumn('options', function ($item) {

                            $back = "";
                            if (\Permissions::check('supervisors_jobs', 'edit') || \Permissions::check('supervisors_jobs', 'delete')) {
                                $back .= '<div class="btn-group">';
                                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> options';
                                $back .= '<i class="fa fa-angle-down"></i>';
                                $back .= '</button>';
                                $back .= '<ul class = "dropdown-menu" role = "menu">';

                                if (\Permissions::check('supervisors_jobs', 'edit')) {
                                    $back .= '<li>';
                                    $back .= '<a href="'.route('supervisors_jobs.edit',$item->id).'">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                if (\Permissions::check('supervisors_jobs', 'delete')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" data-toggle="confirmation" onclick = "SupervisorJobs.delete(this);return false;" data-id = "' . $item->id . '">';
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
