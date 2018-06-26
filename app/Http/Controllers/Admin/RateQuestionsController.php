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

class RateQuestionsController extends BackendController {

    private $rules = array(
        
    );

    public function __construct() {
        parent::__construct();
        $this->middleware('CheckPermission:rate_question,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:rate_question,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:rate_question,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:rate_question,delete', ['only' => ['delete']]);
    }

    private function question_rules($columns_arr = array()) {
        $rules = array();
         $rules['this_order'] = 'required';
        if (!empty($columns_arr)) {
            foreach ($columns_arr as $column => $rule) {
                foreach ($this->languages as $lang_key => $locale) {
                    $key = 'translations.' . $lang_key . '.' . $column;
                    $rules[$key] = $rule;
                }
            }
        }
        return $rules;
    }

    private function answers_rules($answers = array()) {
        $rules = array();

        if (!empty($answers)) {
            foreach ($answers as $index => $one) {

                $rules[ 'answers.' . $index . '.order'] = 'required';
                if (!empty($one['translations'])) {
                    foreach ($one['translations'] as $lang_code => $one2) {
                        $rules[ 'answers.' . $index . '.translations.'.$lang_code.'.title'] = 'required';
                    }
                }
            }
        }
        return $rules;
    }

    public function index() {

        return $this->_view('rate_question/index', 'backend');
    }

    public function create() {
        return $this->_view('rate_question/create', 'backend');
    }

    public function store(Request $request) {
//        dd($this->answers_rules($request->input('answers')));
        $validator = Validator::make($request->all(), array_merge($this->question_rules(['title']), $this->answers_rules($request->input('answers'))));

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
            $translations = $request->input('translations');
            $answers = $request->input('answers');
           
            
            foreach ($translations as $key => $value) {
                $rateQuestionTranslations[] = [
                    'locale' => $key,
                    'title' => $value['title'],
                    'rate_question_id' => $rateQuestion->id
                ];
            }
            RateQuestionTranslation::insert($rateQuestionTranslations);



            if (count($answers) > 0) {
                foreach ($answers as $value2) {
                    //dd($value);
                    $rateQuestionAnswer = new RateQuestionAnswer;
                    $rateQuestionAnswer->this_order = $value2['order'];
                    $rateQuestionAnswer->rate_question_id = $rateQuestion->id;
                    $rateQuestionAnswer->save();
                    $rateQuestionAnswerTranslations = array();
                    foreach ($value2['translations'] as $key2 => $value3) {
                        $rateQuestionAnswerTranslations[] = array(
                            'locale' => $key2,
                            'title' => $value3['title'],
                            'rate_question_answer_id' => $rateQuestionAnswer->id
                        );
                    }
                    RateQuestionAnswerTranslation::insert($rateQuestionAnswerTranslations);
                }
            }


            DB::commit();
            return _json('success', _lang('app.added_successfully'));
        } catch (\Exception $ex) {
            dd($ex);
            DB::rollback();
            return _json('error', _lang('app.error_is_occured'), 400);
        }
    }

    public function edit($id) {
        $RateQuestion = RateQuestion::find($id);
        if (!$RateQuestion) {
            return $this->err404();
        }
        $RateQuestion->translations = RateQuestionTranslation::where('rate_question_id', $id)->get()->keyBy('locale');
        $rateQuestionAnswers = RateQuestionAnswer::where('rate_question_id', $id)->get();

        foreach ($rateQuestionAnswers as $one) {
            $one->translations = RateQuestionAnswerTranslation::where('rate_question_answer_id', $one->id)->get()->keyBy('locale');
        }

        $this->data['question'] = $RateQuestion;
        $this->data['answers'] = $rateQuestionAnswers;
//        dd($this->data['answers']);
        return $this->_view('rate_question/edit', 'backend');
    }

    public function edit2($id) {
        $find = RateQuestion::find($id);
        if (!$find) {
            return $this->err404();
        }
        $rateQuestionAnswer = RateQuestionAnswer::where('rate_question_id', $id)->get();
        // dd($rateQuestionAnswer);
        $rateQuestionTranslations = RateQuestionTranslation::where('rate_question_id', $id)->get();
        $titles = $rateQuestionTranslations->pluck('title', 'locale')->all();
        $titles['order'] = $find->this_order;
        $titles['id'] = $id;
        foreach ($rateQuestionAnswer as $value) {
            $answer = array();
            $rateQuestionAnswerTranslation = RateQuestionAnswerTranslation::where('rate_question_answer_id', $value->id)->get();
            $answer = $rateQuestionAnswerTranslation->pluck('title', 'locale')->all();
            $answer['order'] = $value->this_order;
            $answer['id'] = $value->id;
            $answers[] = $answer;
        }
//        dd($answers);
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
//        $columns_arr = array(
//            'title' => 'required|unique:rate_questions_translations,title,' . $id . ',rate_question_id',
//        );
//        $lang_rules = $this->lang_rules($columns_arr);
//        $this->rules = array_merge($this->rules, $lang_rules);
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }
        DB::beginTransaction();
        try {
            $rateQuestion->active = $request->input('active');
            $rateQuestion->this_order = $request->input('this_order');
            $rateQuestion->save();

            $translations = $request->input('translations');
            $answers = $request->input('answers');
            $question_translations_new = [];
            $question_translations_old_update = [];
            $answers_new = [];
            $answers_old_update = [];
            $answers_translations_new = [];
            $answers_translations_old_update = [];

            foreach ($translations as $key => $value) {
                if ($value['id']) {
                    $question_translations_old_update['title'][] = [
                        'id' => $value['id'],
                        'value' => $value['title']
                    ];
                } else {
                    $question_translations_new[] = [
                        'locale' => $key,
                        'title' => $value['title'],
                        'rate_question_id' => $rateQuestion->id
                    ];
                }
            }
            //dd($answers);
            foreach ($answers as $one) {
                if ($one['id']) {
                    $answers_old_update['this_order'][] = [
                        'id' => $one['id'],
                        'value' => $one['order']
                    ];
                } else {
                    $answers_new[] = $one;
                    continue;
                }

                if (count($one['translations']) > 0) {
                    foreach ($one['translations'] as $key2 => $value2) {

                        if ($value2['id']) {
                            $answers_translations_old_update['title'][] = [
                                'id' => $value2['id'],
                                'value' => $value2['title']
                            ];
                        } else {
                            //dd('here');
                            $answers_translations_new[] = [
                                'locale' => $key2,
                                'title' => $value2['title'],
                                'rate_question_answer_id' => $one['id']
                            ];
                        }
                    }
                }
            }
            if (count($question_translations_old_update) > 0) {
                $this->updateValues('\App\Models\RateQuestionTranslation', $question_translations_old_update);
            }
            if (count($question_translations_new) > 0) {
                RateQuestionTranslation::insert($question_translations_new);
            }
            if (count($answers_old_update) > 0) {
                $this->updateValues('\App\Models\RateQuestionAnswer', $answers_old_update);
            }
            if (count($answers_translations_old_update) > 0) {
                $this->updateValues('\App\Models\RateQuestionAnswerTranslation', $answers_translations_old_update);
            }
            //dd($answers_translations_new);
            if (count($answers_translations_new) > 0) {
                RateQuestionAnswerTranslation::insert($answers_translations_new);
            }
            if (count($answers_new) > 0) {
                foreach ($answers_new as $value3) {
                    //dd($value3);
                    $rateQuestionAnswer = new RateQuestionAnswer;
                    $rateQuestionAnswer->this_order = $value3['order'];
                    $rateQuestionAnswer->rate_question_id = $rateQuestion->id;
                    $rateQuestionAnswer->save();
                    $rateQuestionAnswerTranslations = array();
                    foreach ($value3['translations'] as $key4 => $value4) {
                        $rateQuestionAnswerTranslations[] = array(
                            'locale' => $key4,
                            'title' => $value4['title'],
                            'rate_question_answer_id' => $rateQuestionAnswer->id
                        );
                    }
                    RateQuestionAnswerTranslation::insert($rateQuestionAnswerTranslations);
                }
            }




            DB::commit();
            return _json('success', _lang('app.added_successfully'));
        } catch (\Exception $ex) {
            dd($ex);
            DB::rollback();
            return _json('error', _lang('app.error_is_occured'), 400);
        }
    }

    public function update2(Request $request, $id) {
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
        $answers = $request->input('answers');
        $order = $request->input('order');
        $answers_ids = $request->input('answers_ids');
        $count = 0;
        dd($answers);
        // sort($answers);
        foreach ($order as $value) {
            $answer = $answers[$count];
            $rateQuestionAnswer = RateQuestionAnswer::where('id', $answer['id'])->first();
            $rateQuestionAnswer->this_order = $value;
            $rateQuestionAnswer->save();
            RateQuestionAnswerTranslation::where('rate_question_answer_id', $rateQuestionAnswer->id)->delete();

            $rateQuestionAnswerTranslations = array();
            foreach ($this->languages as $key => $value) {
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

//             $lounges_update['title'][] = [
//                            'id' => 717,
//                            'value' => 4
//                        ];
//        $this->updateValues('\App\Models\RateQuestionAnswerTranslation',$lounges_update);


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
            $rateQuestionAnswer = RateQuestionAnswer::where('rate_question_id', $rateQuestion->id)->get();
            foreach ($rateQuestionAnswer as $value) {
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

    public function destroy_answer($id) {
        $RateQuestionAnswer = RateQuestionAnswer::find($id);
        //dd($RateQuestionAnswer);
        if (!$RateQuestionAnswer) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        DB::beginTransaction();
        try {
            $RateQuestionAnswer->delete();
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
