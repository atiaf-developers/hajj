<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\RateQuestion;
use App\Models\RateQuestionAnswer;
use App\Models\RateQuestionTranslation;
use App\Models\RateQuestionAnswerTranslation;
use Validator;
use DB;

class RateQuestionsController extends BackendController
{
    private $rules = array(
        'this_order' => 'required',
    );

    public function __construct() {
        parent::__construct();
        $this->middleware('CheckPermission:rate_question,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:rate_question,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:rate_question,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:rate_question,delete', ['only' => ['delete']]);
    }
    public function index() {

        return $this->_view('rate_question/index', 'backend');
    }

    public function create() {
        return $this->_view('rate_question/create', 'backend');
    }
    public function store(Request $request) {
        $columns_arr = array(
            'title' => 'required|unique:rate_questions_translations,title',
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
            $rateQuestion = new RateQuestion;
            $rateQuestion->active = $request->input('active');
            $rateQuestion->this_order = $request->input('this_order');
            $rateQuestion->save();

            $rateQuestionTranslations = array();
            foreach ($request->input('title') as $key => $value) {
                $rateQuestionTranslations[] = array(
                    'locale' => $key,
                    'title' => $value,
                    'rate_question_id' => $rateQuestion->id
                );
            }
            RateQuestionTranslation::insert($rateQuestionTranslations);
            $answers=$request->input('answers');
            $order=$request->input('order');
            $count=0;
            foreach($order as $value){
                
                $rateQuestionAnswer = new RateQuestionAnswer;
                $rateQuestionAnswer->this_order = $value;
                $rateQuestionAnswer->rate_question_id = $rateQuestion->id;
                $rateQuestionAnswer->save();
                $rateQuestionAnswerTranslations=array();
                foreach($this->languages as $key=>$value){
                    $rateQuestionAnswerTranslations[] = array(
                        'locale' => $key,
                        'title' => $answers[$count][$key],
                        'rate_question_answer_id' => $rateQuestionAnswer->id
                    );
                }
                RateQuestionAnswerTranslation::insert($rateQuestionAnswerTranslations);
                $count++;
            }
            DB::commit();
            return _json('success', _lang('app.added_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            return _json('error', _lang('app.error_is_occured'), 400);
        }
    }
    public function edit($id) {
        $find = RateQuestion::find($id);
        if (!$find) {
            return $this->err404();
        }
        $rateQuestionAnswer = RateQuestionAnswer::where('rate_question_id', $id)->get();
        // dd($rateQuestionAnswer);
        $rateQuestionTranslations = RateQuestionTranslation::where('rate_question_id', $id)->get();
        $titles = $rateQuestionTranslations->pluck('title', 'locale')->all();
        $titles['order']=$find->this_order;
        $titles['id']=$id;
        foreach($rateQuestionAnswer as $value){
            $answer=array();
            $rateQuestionAnswerTranslation = RateQuestionAnswerTranslation::where('rate_question_answer_id', $value->id)->get();
            $answer = $rateQuestionAnswerTranslation->pluck('title', 'locale')->all();
            $answer['order']=$value->this_order;
            $answer['id']=$value->id;
            $answers[]=$answer;
        }
        // dd($answers);
        $this->data['data'] = $find;
        $this->data['titles'] = $titles;
        $this->data['answers'] = $answers;
        return $this->_view('rate_question/edit', 'backend');
    }
    public function update(Request $request, $id) {
        // dd($request);
        $rateQuestion = RateQuestion::find($id);

        if (!$rateQuestion) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        $columns_arr = array(
            'title' => 'required|unique:rate_questions_translations,title,' . $id . ',rate_question_id',
        );
        $lang_rules = $this->lang_rules($columns_arr);
        $this->rules = array_merge($this->rules, $lang_rules);
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }
        DB::beginTransaction();
        // try {
            $rateQuestion->active = $request->input('active');
            $rateQuestion->this_order = $request->input('this_order');
            $rateQuestion->save();

            $rateQuestionTranslations = array();
            RateQuestionTranslation::where('rate_question_id', $rateQuestion->id)->delete();
            foreach ($request->input('title') as $key => $value) {
                $rateQuestionTranslations[] = array(
                    'locale' => $key,
                    'title' => $value,
                    'rate_question_id' => $rateQuestion->id
                );
            }
            RateQuestionTranslation::insert($rateQuestionTranslations);
            $answers=$request->input('answers');
            $order=$request->input('order');
            $count=0;
            // dd($order);
            // sort($answers);
            foreach($order as $value){
                $answer=$answers[$count];
                $rateQuestionAnswer =RateQuestionAnswer::where('id',$answer['id'])->first();
                $rateQuestionAnswer->this_order = $value;
                $rateQuestionAnswer->save();
                RateQuestionAnswerTranslation::where('rate_question_answer_id', $rateQuestionAnswer->id)->delete();
                
                $rateQuestionAnswerTranslations=array();
                foreach($this->languages as $key=>$value){
                    $rateQuestionAnswerTranslations[] = array(
                        'locale' => $key,
                        'title' => $answers[$count][$key],
                        'rate_question_answer_id' => $rateQuestionAnswer->id
                    );
                }
                RateQuestionAnswerTranslation::insert($rateQuestionAnswerTranslations);
                $count++;
            }
            DB::commit();
            return _json('success', _lang('app.added_successfully'));
        // } catch (\Exception $ex) {
        //     DB::rollback();
        //     return _json('error', _lang('app.error_is_occured'), 400);
        // }
    }
    public function destroy($id) {
        $rateQuestion = RateQuestion::find($id);
        if (!$rateQuestion) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        DB::beginTransaction();
        try {
            $rateQuestionAnswer = RateQuestionAnswer::where('rate_question_id',$rateQuestion->id)->get();
            foreach($rateQuestionAnswer as $value){
                RateQuestionAnswerTranslation::where('rate_question_answer_id', $value->id)->delete();
            }
            RateQuestionAnswer::where('rate_question_id', $rateQuestion->id)->delete();

            RateQuestionTranslation::where('rate_question_id', $rateQuestion->id)->delete();
            $rateQuestion->delete();
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
    public function data(Request $request){
        $question = RateQuestion::Join('rate_questions_translations', 'rate_questions.id', '=', 'rate_questions_translations.rate_question_id')
        ->where('rate_questions_translations.locale', $this->lang_code)
        ->select([
        'rate_questions.id', 'rate_questions.active', "rate_questions_translations.title as question", "rate_questions.this_order"
        ]);

        return \Datatables::eloquent($question)
                ->addColumn('options', function ($item) {

                    $back = "";
                    if (\Permissions::check('rate_question', 'edit') || \Permissions::check('rate_question', 'delete')) {
                        $back .= '<div class="btn-group">';
                        $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> options';
                        $back .= '<i class="fa fa-angle-down"></i>';
                        $back .= '</button>';
                        $back .= '<ul class = "dropdown-menu" role = "menu">';
                        if (\Permissions::check('rate_question', 'edit')) {
                            $back .= '<li>';
                            $back .= '<a href="' . route('rate_question.edit', $item->id) . '">';
                            $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                            $back .= '</a>';
                            $back .= '</li>';
                        }

                        if (\Permissions::check('rate_question', 'delete')) {
                            $back .= '<li>';
                            $back .= '<a href="" data-toggle="confirmation" onclick = "RateQuestions.delete(this);return false;" data-id = "' . $item->id . '">';
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

    }
   
    
}
