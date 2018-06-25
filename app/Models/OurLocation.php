<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OurLocation extends MyModel
{
    protected $table = "our_locations";


    public static function transform($item)
	{
		$transformer = new \stdClass();
		$transformer->id = $item->id;
		$transformer->location_image =  url('public/uploads/our_locations') . '/' . $item->location_image;
		$transformer->title = $item->title;
		$transformer->address = $item->address;
		$transformer->lat = $item->lat;
		$transformer->lng = $item->lng;
		$transformer->contact_numbers = explode(",",$item->contact_numbers);

       return $transformer;
		
	}
}
