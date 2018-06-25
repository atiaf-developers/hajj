<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RateQuestionAnswer extends MyModel
{
    protected $table = "rate_question_answers";

    public static function transform($item){
        
        $lang =  static::getLangCode();
        $transformer = new \stdClass();
        $transformer->id =  $item->id;
        $transformer->question = $item->title;

       return $transformer;
        
    }



}
