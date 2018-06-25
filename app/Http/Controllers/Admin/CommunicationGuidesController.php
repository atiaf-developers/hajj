<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\CommunicationGuide;
use App\Models\CommunicationGuideTranslation;
use Validator;
use DB;

class CommunicationGuidesController extends BackendController {

    private $rules = array(
        'active' => 'required',
        'this_order' => 'required',
    );

    public function __construct() {

        parent::__construct();
        $this->middleware('CheckPermission:communication_guides,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:communication_guides,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:communication_guides,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:communication_guides,delete', ['only' => ['delete']]);
    }

    public function index(Request $request) {
        return $this->_view('communication_guides/index', 'backend');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        return $this->_view('communication_guides/create', 'backend');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $columns_arr = array(
            'title' => 'required',
            'description' => 'required',
        );

        $lang_rules = $this->lang_rules($columns_arr);
        $this->rules = array_merge($this->rules, $lang_rules);

        if ($request->file('image')) {
            $this->rules['image'] = 'required|image|mimes:gif,png,jpeg|max:1000';
        }
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }
        DB::beginTransaction();
        try {
          

            $communication_guide = new CommunicationGuide;

            $communication_guide->active = $request->input('active');
            $communication_guide->this_order = $request->input('this_order');

            if ($request->file('image')) {
                $communication_guide->image = CommunicationGuide::upload($request->file('image'), 'communication_guides', true);
            }
            $communication_guide->save();

            $communication_guide_translations = array();

            $title_translations = $request->input('title');
            $description_translations = $request->input('description');
            

            foreach ($this->languages as $key => $value) {
                $communication_guide_translations[] = array(
                    'locale' => $key,
                    'title' => $title_translations[$key],
                    'description' => $description_translations[$key],
                    'communication_guide_id' => $communication_guide->id
                );
            }

            CommunicationGuideTranslation::insert($communication_guide_translations);

            DB::commit();
            return _json('success', _lang('app.added_successfully'));
        } catch (\Exception $ex) {
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
        $find = CommunicationGuide::find($id);

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
        $communication_guide = CommunicationGuide::find($id);
              
        if (!$communication_guide) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
       
        $this->data['title_translations'] = CommunicationGuideTranslation::where('communication_guide_id', $id)->pluck('title', 'locale');
        $this->data['description_translations'] = CommunicationGuideTranslation::where('communication_guide_id', $id)->pluck('description', 'locale');
        $this->data['communication_guide'] = $communication_guide;

        return $this->_view('communication_guides/edit', 'backend');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {


        $communication_guide = CommunicationGuide::find($id);
        if (!$communication_guide) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }

        $columns_arr = array(
            'title' => 'required',
            'description' => 'required',
        );
        
        $lang_rules = $this->lang_rules($columns_arr);
        $this->rules = array_merge($this->rules, $lang_rules);

        if ($request->file('image')) {
            $this->rules['image'] = 'required|image|mimes:gif,png,jpeg|max:1000';
        }


        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }


        DB::beginTransaction();
        try {

            $communication_guide->active = $request->input('active');
            $communication_guide->this_order = $request->input('this_order');

            if ($request->file('image')) {

                if ($communication_guide->image) {
                  $old_image = $communication_guide->image;
                  CommunicationGuide::deleteUploaded('communication_guides', $old_image);
                }

                $communication_guide->image = CommunicationGuide::upload($request->file('image'), 'communication_guides', true);
            }
            $communication_guide->save();

            CommunicationGuideTranslation::where('communication_guide_id', $communication_guide->id)->delete();

            $communication_guide_translations = array();
            $title_translations = $request->input('title');
            $description_translations = $request->input('description');
            

            foreach ($this->languages as $key => $value) {
                $communication_guide_translations[] = array(
                    'locale' => $key,
                    'title' => $title_translations[$key],
                    'description' => $description_translations[$key],
                    'communication_guide_id' => $communication_guide->id
                );
            }
            CommunicationGuideTranslation::insert($communication_guide_translations);
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
        $communication_guide = CommunicationGuide::find($id);
        if (!$communication_guide) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        DB::beginTransaction();
        try {
            $communication_guide->delete();
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
       
        $communication_guides = CommunicationGuide::Join('communication_guides_translations', 'communication_guides.id', '=', 'communication_guides_translations.communication_guide_id')
                ->where('communication_guides_translations.locale', $this->lang_code)
                ->select([
            'communication_guides.id', "communication_guides_translations.title", "communication_guides.this_order","communication_guides.active"
        ]);

        return \Datatables::eloquent($communication_guides)
                        ->addColumn('options', function ($item) {

                            $back = "";
                            if (\Permissions::check('communication_guides', 'edit') || \Permissions::check('communication_guides', 'delete')) {
                                $back .= '<div class="btn-group">';
                                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> options';
                                $back .= '<i class="fa fa-angle-down"></i>';
                                $back .= '</button>';
                                $back .= '<ul class = "dropdown-menu" role = "menu">';
                                if (\Permissions::check('communication_guides', 'edit')) {
                                    $back .= '<li>';
                                    $back .= '<a href="' . route('communication_guides.edit', $item->id) . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                if (\Permissions::check('communication_guides', 'delete')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" data-toggle="confirmation" onclick = "CommunicationGuides.delete(this);return false;" data-id = "' . $item->id . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.delete');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                if (\Permissions::check('communication_guide_supervisors', 'add')) {
                                    $back .= '<li>';
                                    $back .= '<a href="'.route('communication_guides_supervisors.index').'?communication_guide='.$item->id.'"  " data-id = "' . $item->id . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.supervisors');
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
                        ->escapeColumns([])
                        ->make(true);
    }

}
