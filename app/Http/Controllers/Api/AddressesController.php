<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Validator;
use App\Models\Address;

class AddressesController extends ApiController {

    private $rules = array(
        'name' => 'required',
        'mobile' => 'required|numeric',
        'city' => 'required',
        'street' => 'required',
        'building' => 'required',
        'floor_number' => 'required',
    );

    public function __construct() {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        try {
            $user = $this->auth_user();
            $addresses = Address::join('locations as city','city.id','=','addresses.city_id')
                    ->join('locations as country','country.id','=','city.parent_id')
                    ->select(["addresses.id",'addresses.name','addresses.mobile','addresses.building','addresses.floor_number',
                        'addresses.street',"city.title_$this->lang_code as city_title","country.title_$this->lang_code as country_title","city.delivery_fees"])
                    ->where('user_id', $user->id)
                    ->paginate($this->limit);

            return _api_json(Address::transformCollection($addresses));
        } catch (\Exception $e) {
            $message = _lang('app.error_is_occured');
            return _api_json([], ['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        try {
            $validator = Validator::make($request->all(), $this->rules);
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return _api_json('', ['errors' => $errors], 422);
            }
            $address = new Address;
            $address->name = $request->name;
            $address->mobile = $request->mobile;
            $address->city_id = $request->city;
            $address->street = $request->street;
            $address->building = $request->building;
            $address->floor_number = $request->floor_number;
            $address->user_id = $this->auth_user()->id;
            $address->save();
            return _api_json('', ['message' => _lang('app.added_successfully')]);
        } catch (\Exception $e) {
            $message = _lang('app.error_is_occured');
            return _api_json('', ['message' => $message], 422);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        try {
            $address = Address::where('id', $id)->where('user_id', $this->auth_user()->id)->first();
            if (!$address) {
                $message = _lang('app.not_found');
                return _api_json('', ['message' => $message], 404);
            }
            $validator = Validator::make($request->all(), $this->rules);
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return _api_json('', ['errors' => $errors], 422);
            }

            $address->name = $request->name;
            $address->mobile = $request->mobile;
            $address->city_id = $request->city;
            $address->street = $request->street;
            $address->building = $request->building;
            $address->floor_number = $request->floor_number;
            $address->save();
            return _api_json('', ['message' => _lang('app.updated_successfully')]);
        } catch (\Exception $e) {
            $message = _lang('app.error_is_occured');
            return _api_json('', ['message' => $message], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        try {
            $address = Address::where('id', $id)->where('user_id', $this->auth_user()->id)->first();
            if (!$address) {
                $message = _lang('app.not_found');
                return _api_json('', ['message' => $message], 404);
            }
            $address->delete();
            return _api_json('', ['message' => _lang('app.deleted_successfully')]);
        } catch (\Exception $e) {
            $message = _lang('app.error_is_occured');
            return _api_json('', ['message' => $message], 422);
        }
    }

}
