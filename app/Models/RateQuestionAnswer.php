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
    
    public function translations() {
        return $this->hasMany(RateQuestionAnswerTranslation::class, 'rate_question_answer_id');
    }


    protected static function boot() {
        parent::boot();

        static::deleting(function($RateQuestionAnswer) {
            foreach ($RateQuestionAnswer->translations as $translation) {
                $translation->delete();
            }
        });
        static::deleted(function($pilgrim_class) {
        });
    }



}
