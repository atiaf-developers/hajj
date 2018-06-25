<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends MyModel
{
    protected $table = "news";

	public function translations() {
		return $this->hasMany(NewsTranslation::class, 'news_id');
	}

	public static function transform($item)
	{
		$transformer = new \stdClass();
		$transformer->image = $item->image ? url('public/uploads/news') . '/' . $item->image : '';
		$transformer->description = $item->description;
		$transformer->created_at = date('d/m/Y',strtotime($item->created_at));

       return $transformer;
		
	}



	protected static function boot() {
		parent::boot();

		static::deleting(function($news) {
			foreach ($news->translations as $translation) {
				$translation->delete();
			}
		});

		static::deleted(function($news) {
			if ($news->image) {
				$old_image = $news->image;
                static::deleteUploaded('news', $old_image);
			}
		});
	}


}
