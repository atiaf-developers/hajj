<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\CommonQuestion;
use App\Models\CommonQuestionTranslation;
use Validator;
use DB;

class CommonQuestionController extends BackendController {

    private $rules = array(
        'this_order' => 'required',
    );

    public function __construct() {
        parent::__construct();
        $this->middleware('CheckPermission:common_question,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:common_question,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:common_question,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:common_question,delete', ['only' => ['delete']]);
    }

    public function index() {

        return $this->_view('common_question/index', 'backend');
    }

    public function create() {
        return $this->_view('common_question/create', 'backend');
    }

    public function store(Request $request) {
       
        $columns_arr = array(
            'question' => 'required|unique:common_questions_translations,question',
            'answer' => 'required|unique:common_questions_translations,answer'
        );
        $lang_rules = $this->lang_rules($columns_arr);
        $this->rules = array_merge($this->rules, $lang_rules);
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }
        // dd($validator);
        DB::beginTransaction();
        try {

            $commonQuestion = new CommonQuestion;
            $commonQuestion->active = $request->input('active');
            $commonQuestion->this_order = $request->input('this_order');

            $commonQuestion->save();

            $commonQuestion_translations = array();
            $answers = $request->input('answer');
            foreach ($request->input('question') as $key => $value) {
                $location_translations[] = array(
                    'locale' => $key,
                    'question' => $value,
                    'answer' => $answers[$key],
                    'common_question_id' => $commonQuestion->id
                );
            }
            CommonQuestionTranslation::insert($location_translations);
            DB::commit();
            return _json('success', _lang('app.added_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            return _json('error', _lang('app.error_is_occured'), 400);
        }
    }

    public function edit($id) {
        $find = CommonQuestion::find($id);
        if (!$find) {
            return $this->err404();
        }
        $commonQuestionTranslation = CommonQuestionTranslation::where('common_question_id', $id)->get();
        $questions = $commonQuestionTranslation->pluck('question', 'locale')->all();
        $answers = $commonQuestionTranslation->pluck('answer', 'locale')->all();
        //dd($questions);
        // dd($answers);
        $this->data['data'] = $find;
        $this->data['questions'] = $questions;
        $this->data['answers'] = $answers;
        return $this->_view('common_question/edit', 'backend');
    }

    public function update(Request $request, $id) {
        $commonQuestion = CommonQuestion::find($id);

        if (!$commonQuestion) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        $columns_arr = array(
            'question' => 'required|unique:common_questions_translations,question,' . $id . ',common_question_id',
            'answer' => 'required|unique:common_questions_translations,answer,' . $id . ',common_question_id'
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
            $commonQuestion->active = $request->input('active');
            $commonQuestion->this_order = $request->input('this_order');
            $commonQuestion->save();

            CommonQuestionTranslation::where('common_question_id', $commonQuestion->id)->delete();

            $commonQuestion_translations = array();
            $answers = $request->input('answer');
            foreach ($request->input('question') as $key => $value) {
                $location_translations[] = array(
                    'locale' => $key,
                    'question' => $value,
                    'answer' => $answers[$key],
                    'common_question_id' => $commonQuestion->id
                );
            }
            CommonQuestionTranslation::insert($location_translations);
            DB::commit();
            return _json('success', _lang('app.updated_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            return _json('error', _lang('app.error_is_occured'), 400);
        }
    }

    public function destroy($id) {
        $commonQuestion = CommonQuestion::find($id);
        if (!$commonQuestion) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        DB::beginTransaction();
        try {
            CommonQuestionTranslation::where('common_question_id', $commonQuestion->id)->delete();
            $commonQuestion->delete();
            DB::commit();
            return _json('success', _lang('app.deleted_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            return _json('error', _lang('app.error_is_occured'), 400);
            // if ($ex->getCode() == 23000) {
            //     return _json('error', _lang('app.this_record_can_not_be_deleted_for_linking_to_other_records'), 400);
            // } else {
            //     return _json('error', _lang('app.error_is_occured'), 400);
            // }
        }
    }

    public function data(Request $request) {
        $locations = CommonQuestion::Join('common_questions_translations', 'common_questions.id', '=', 'common_questions_translations.common_question_id')
                ->where('common_questions_translations.locale', $this->lang_code)
                ->select([
            'common_questions.id', 'common_questions.active', "common_questions_translations.question", "common_questions.this_order"
        ]);

        return \Datatables::eloquent($locations)
                        ->addColumn('options', function ($item) {

                            $back = "";
                            if (\Permissions::check('common_question', 'edit') || \Permissions::check('common_question', 'delete')) {
                                $back .= '<div class="btn-group">';
                                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> options';
                                $back .= '<i class="fa fa-angle-down"></i>';
                                $back .= '</button>';
                                $back .= '<ul class = "dropdown-menu" role = "menu">';
                                if (\Permissions::check('common_question', 'edit')) {
                                    $back .= '<li>';
                                    $back .= '<a href="' . route('common_question.edit', $item->id) . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                if (\Permissions::check('common_question', 'delete')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" data-toggle="confirmation" onclick = "CommonQuestions.delete(this);return false;" data-id = "' . $item->id . '">';
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
                        ->rawColumns(['options', 'active'])
                        ->make(true);
        ;
    }

}
