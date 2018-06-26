<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends MyModel {

    protected $table = "categories";

   public function childrens()
    {
        return $this->hasMany(Category::class,'parent_id');
    }

    public function translations()
    {
        return $this->hasMany(CategoryTranslation::class,'category_id');
    }

    public static function transform($item)
    {
        $lang =  static::getLangCode();
        $transformer = new \stdClass();
        $transformer->id = $item->id;
        $transformer->title = $item->title;

        if ($item->parent_id == 0) { 

            $transformer->sub_categories = Category::transformCollection(
                $item->childrens()
                ->join('categories_translations','categories.id','=','categories_translations.category_id')
                ->where('categories_translations.locale',$lang)
                ->where('categories.active',true)
                ->select('categories.id','categories_translations.title','categories_translations.description','categories.pdf','categories.pdf_status','categories.parent_id')
                ->get()
            );
            
        }
        else {
            $transformer->description = $item->description; 
            $transformer->pdf = url('public/uploads/categories/'.$item->pdf); 
            $transformer->pdf_status = $item->pdf_status; 
        }
    
        return $transformer;
        
    }

   

    protected static function boot() {
        parent::boot();

       static::deleting(function($category) {

            foreach ($category->childrens as $child) {
                foreach ($category->translations as $translation) {
                    $translation->delete();
                 }
                $child->delete();
            }

            foreach ($category->translations as $translation) {
                    $translation->delete();
            }

        });
    }

}
