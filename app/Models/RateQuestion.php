<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RateQuestion extends MyModel
{
    protected $table = "rate_questions";


    public function answers()
    {
    	return $this->hasMany(RateQuestionAnswer::class,'rate_question_id');
    }


    public static function transform($item){
        
        $lang =  static::getLangCode();
        $transformer = new \stdClass();
        $transformer->id =  $item->id;
        $transformer->question = $item->title;
        $transformer->answers = RateQuestionAnswer::transformCollection(
        	$item->answers()
        	->Join('rate_question_answers_translations','rate_question_answers.id','=','rate_question_answers_translations.rate_question_answer_id')
        	->where('rate_question_answers_translations.locale',$lang)
        	->orderBy('rate_question_answers.this_order')
        	->select('rate_question_answers.id','rate_question_answers_translations.title')
        	->get());

       return $transformer;
        
    }

}
