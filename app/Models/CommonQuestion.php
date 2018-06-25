<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommonQuestion extends MyModel
{
    protected $table = "common_questions";

    public static function transform($item){

        $transformer = new \stdClass();
        $transformer->id =  $item->id;
        $transformer->question = $item->question;
        $transformer->answer = $item->answer;

       return $transformer;
        
    }

    public function translation($lang){
        return $this->hasOne(CommenQuestionTranslation::class)->where('locale',$lang);
    }
}


