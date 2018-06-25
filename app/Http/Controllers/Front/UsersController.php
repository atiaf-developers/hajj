<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\FrontController;
use App\Models\User;
use App\Models\Meal;
use Validator;


class UsersController extends FrontController {
    

    private $rules = array(
        'first_name' => 'required',
        'last_name' => 'required',
    );
    public function __construct() {
        parent::__construct();
        $this->middleware('auth');
    }

    public function profile()
    {
        try {
            return $this->_view('users.profile');
        } catch (\Exception $e) {

            session()->flash('msg',_lang('app.error_is_occured_try_again_later'));
            return redirect()->back();
        }
    }


    public function editProfile()
    {
        try {
            return $this->_view('users.edit_profile');
        } catch (\Exception $e) {
            session()->flash('msg',_lang('app.error_is_occured_try_again_later'));
            return redirect()->back();
        }
    }


    public function updateProfile(Request $request)
    {
        try {

        $User = $this->User;
        
        $this->rules['email'] = "required|email|unique:users,email,$User->id";
        $this->rules['mobile'] = "required|unique:users,mobile,$User->id";
        
        if ($request->file('user_image')) {
            $this->rules['user_image'] = "required|image|mimes:gif,png,jpeg|max:1000";
        }
        if ($request->input('password')) {
            $this->rules['password'] = "required";
            $this->rules['confirm_password'] = "required|same:password";
        }
        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {

            if ($request->ajax()) {
                $errors = $validator->errors()->toArray();
                return _json('error',$errors);
            } else {
                return redirect()->back()->withInput($request->only('email'))->withErrors($validator->errors()->toArray());
            }
        }
            
            $User->first_name = $request->input('first_name');
            $User->last_name = $request->input('last_name');
            $User->mobile = $request->input('mobile');
            $User->email = $request->input('email');

            if ($request->input('password')) {     
             $User->password = bcrypt($request->input('password'));
            }
            if ($request->file('user_image')) {
                    $file = url("public/uploads/users/$User->user_image");
                    if (!is_dir($file)) {
                        if (file_exists($file)) {
                            unlink($file);
                        }
                    }

              $User->user_image = $this->_upload($request->file('user_image'),'users');
            }
           $User->save();
           if ($request->ajax()) {
               return _json('success',_lang('app.updated_successfully'));
           } 
           session()->flash('msg',_lang('app.updated_successfully'));
           return redirect()->back();
            
        } catch (\Exception $e) {

            session()->flash('msg',_lang('app.error_is_occured_try_again_later'));
            return redirect()->back();
        }
    }

    public function addDeleteFavourite(Request $request,$meal_slug)
    {
        try {
            $user = $this->User;
            
            $Meal = Meal::where('slug',$meal_slug)->where('active',true)->first();
            if (!$Meal) {
                if ($request->ajax) {
                   return _json('error',_lang('app.error_is_occured'));
                }
                return $this->err404();
            }

            if ($user->favourites->contains($Meal->id)) {
                $user->favourites()->detach($Meal->id);
                $message = false;
            }
            else{
                $user->favourites()->attach($Meal);
                 $message = true;
            }

            if ($request->ajax()) {
               return _json('success', $message);
           }
          session()->flash('msg',_lang('app.done'));
          return redirect()->route('user-favourites');
        } catch (\Exception $e) {
            if ($request->ajax) {
                return _json('error',_lang('app.error_is_occured'));
            }
            session()->flash('msg',_lang('app.error_is_occured_try_again_later'));
            return redirect()->back();
        }
    }

    public function favourites()
    {
        $user = $this->User;
        $favourites = Meal::join('favourites','favourites.meal_id','=','meals.id')
                            ->join('menu_sections','menu_sections.id','=','meals.menu_section_id')
                            ->join('resturantes','resturantes.id','=','menu_sections.resturant_id')
                            ->where('favourites.user_id',$user->id)
                            ->select('resturantes.id as resturant_id','meals.id as meal_id','meals.slug as meal_slug','menu_sections.slug as menu_section_slug','resturantes.slug as resturant_slug','meals.image','meals.title_'.$this->lang_code.' as meal','resturantes.title_'.$this->lang_code.' as resturant','meals.price')
                               ->paginate($this->limit);
        $this->data['favourites'] =  $favourites;

        return $this->_view('users.favourites');
    }






}
