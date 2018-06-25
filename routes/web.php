<?php

/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
 */


$languages = array('ar', 'en', 'fr');
$defaultLanguage = 'ar';
if ($defaultLanguage) {
    $defaultLanguageCode = $defaultLanguage;
} else {
    $defaultLanguageCode = 'ar';
}

$currentLanguageCode = Request::segment(1, $defaultLanguageCode);
if (in_array($currentLanguageCode, $languages)) {
    Route::get('/', function () use($defaultLanguageCode) {
        return redirect()->to($defaultLanguageCode);
    });

 
    Route::group(['namespace' => 'Front', 'prefix' => $currentLanguageCode], function () use($currentLanguageCode) {
        app()->setLocale($currentLanguageCode);
        Route::get('/', 'HomeController@index')->name('home');
        Route::get('getRegionByCity/{id}', 'AjaxController@getRegionByCity');
        Route::get('getAddress/{id}', 'AjaxController@getAddress');
        Route::post('location-suggestions', 'AjaxController@search');
        Auth::routes();

        Route::get('user-activation-code', 'Auth\RegisterController@showActivationForm')->name('activation');
        Route::post('activateuser', 'Auth\RegisterController@activate_user')->name('activationuser');

        Route::get('edit-user-phone', 'Auth\RegisterController@showEditMobileForm')->name('edit-phone');
        Route::post('edituserphone', 'Auth\RegisterController@EditPhone')->name('editphone');

        Route::get('login/facebook', 'Auth\RegisterController@redirectToProvider')->name('login/facebook');
        Route::get('login/facebook/callback', 'Auth\RegisterController@handleProviderCallback');
        
        Route::get('complete-registeration', 'Auth\RegisterController@showCompleteRegistrationForm')->name('complete_register');
        

        Route::get('about-us', 'StaticController@about_us')->name('about_us');
        Route::get('usage-and-conditions', 'StaticController@usage_coditions')->name('usage_conditions');
        Route::get('terms-and-conditions', 'StaticController@terms_conditions')->name('terms_conditions');
        Route::get('contact-us', 'StaticController@contact_us')->name('contact_us');
        Route::get('offers', 'StaticController@offers')->name('offers');
        Route::post('contact_us', 'StaticController@sendContactMessage')->name('contact');
       
        Route::get('cart', 'CartController@index');
        Route::post('cart', 'CartController@store');
        Route::get('cart/{index}/remove', 'CartController@remove');
        Route::get('cart/update-quantity', 'CartController@update_quantity');
        Route::get('cart/coupon-check', 'CartController@coupon_check');
        Route::post('cart/new-order', 'CartController@new_order');
        Route::get('resturantes', 'ResturantesController@index');
        Route::get('resturant/{resturant}', 'ResturantesController@resturant');
        Route::get('resturant/{resturant}/{menu_section}', 'ResturantesController@menu');
        Route::get('resturant/{resturant}/{menu_section}/{meal}', 'ResturantesController@meal');
        Route::get('resturantes/cuisines/{cuisine}', 'ResturantesController@getResturantesByCuisine');

        /*************************** user ***************/
        Route::get('user-profile','UsersController@profile')->name('profile');
        Route::get('edit-user-profile','UsersController@editProfile')->name('edit_profile');
        Route::post('update-user','UsersController@updateProfile')->name('update_user');
        Route::get('add-favourite/{slug}','UsersController@addDeleteFavourite')->name('add-favourite');
        Route::get('user-favourites','UsersController@favourites')->name('user-favourites');

        /*************************** addresses ************/
        Route::resource('user-addresses', 'AddressesController');
        
        Route::get('delete-addresses/{id}', 'AddressesController@destroy')->name('delete-address');

        /********************** orders *****************/
        Route::resource('user-orders', 'OrdersController');
        Route::post('rate-order', 'OrdersController@rate')->name('rate_order');
        Route::get('order-meal/update-quantity', 'OrdersController@updateOrderMealQuantity');
        Route::get('order-meal/remove', 'OrdersController@removeOrderMeal');
        
        

    });
} else {
    Route::get('/' . $currentLanguageCode, function () use($defaultLanguageCode) {
        return redirect()->to($defaultLanguageCode);
    });
}


//Route::group(['middleware'=>'auth:admin'], function () {
Route::group(['namespace' => 'Admin', 'prefix' => 'admin'], function () {
    Route::get('/', 'AdminController@index')->name('admin.dashboard');
    Route::get('/error', 'AdminController@error')->name('admin.error');
    Route::get('/change_lang', 'AjaxController@change_lang')->name('ajax.change_lang');
    Route::get('ajax/getPilgrimsForAccommodation', 'AjaxController@getPilgrimsForAccommodation')->name('ajax.getPilgrimsForAccommodation');

     Route::get('profile', 'ProfileController@index');
    Route::patch('profile', 'ProfileController@update');



    Route::resource('groups', 'GroupsController');
    Route::resource('admins', 'AdminsController');

    Route::resource('pilgrims', 'PilgrimsController');
    Route::post('pilgrims/import', 'PilgrimsController@import');
    Route::post('pilgrims/generate_qr', 'PilgrimsController@generateQr');
    Route::post('pilgrims/data', 'PilgrimsController@data');
    Route::resource('locations', 'LocationsController');
    Route::resource('our_locations', 'OurLocationsController');
    Route::resource('categories', 'CategoriesController');
    Route::resource('pilgrims_class', 'PilgrimsClassController');
    Route::resource('pilgrims_buses', 'PilgrimsBusesController');
    Route::resource('managers', 'ManagersController');
    Route::resource('news', 'NewsController');
    Route::resource('tents', 'TentsController');
    Route::resource('communication_guides', 'CommunicationGuidesController');
    Route::resource('communication_guides_supervisors', 'CommunicationGuideSupervisorsController');
    Route::resource('supervisors_jobs', 'SupervisorsJobsController');

    // Route For Common Question Moduel {Start}
    Route::resource('common_question', 'CommonQuestionController');
    Route::post('common_question/data', 'CommonQuestionController@data');
    // Route For Common Question Moduel {End}

    // Route For Rate Question Moduel {Start}
    Route::resource('rate_question', 'RateQuestionsController');
    Route::post('rate_question/data', 'RateQuestionsController@data');
    // Route For Rate Question Moduel {End}
    Route::resource('suites_accommodation', 'SuitesAccommodationController');
    Route::get('manual_accommodation/lounges', 'ManualAccommodationController@getLounges');
    Route::get('manual_accommodation/rooms', 'ManualAccommodationController@getRooms');
    Route::get('manual_accommodation/floors', 'ManualAccommodationController@getFloors');
    Route::resource('manual_accommodation', 'ManualAccommodationController');
    Route::post('manual_accommodation/getDataForAccommodation', 'ManualAccommodationController@getDataForAccommodation');
    Route::resource('buses_accommodation', 'BusesAccommodationController');
    Route::resource('tents_accommodation', 'TentsAccommodationController');
    Route::get('suites_accommodation/lounges/{id}', 'SuitesAccommodationController@getLounges');
    Route::post('suites_accommodation/notify', 'SuitesAccommodationController@notify');
    Route::post('buses_accommodation/notify', 'BusesAccommodationController@notify');
    Route::post('tents_accommodation/notify', 'TentsAccommodationController@notify');
    Route::post('buildings_accommodation/notify', 'BuildingsAccommodationController@notify');
    Route::get('buildings_accommodation/rooms/{id}', 'BuildingsAccommodationController@getRooms');
    Route::get('buildings_accommodation/floors', 'BuildingsAccommodationController@getFloors');
    Route::resource('buildings_accommodation', 'BuildingsAccommodationController');
    Route::resource('suites', 'SuitesController');
    Route::post('suites/data', 'SuitesController@data');
    Route::resource('buildings', 'BuildingsController');
    Route::post('buildings/data', 'BuildingsController@data');
    Route::resource('buildings_floors', 'BuildingsFloorsController');
    Route::post('buildings_floors/data', 'BuildingsFloorsController@data');
    Route::resource('buildings_floors_rooms', 'BuildingsFloorsRoomsController');
    Route::post('buildings_floors_rooms/data', 'BuildingsFloorsRoomsController@data');
    Route::resource('lounges', 'LoungesController');
    Route::post('lounges/data', 'LoungesController@data');

    Route::resource('contact_messages', 'ContactMessagesController');
    
    Route::get('settings', 'SettingsController@index');
    Route::post('settings', 'SettingsController@store');
    Route::get('about_us', 'AboutUsController@index');
    Route::post('about_us', 'AboutUsController@store');
    Route::get('orders_reports', 'OrdersReportsController@index');






    Route::patch('settings/{id}', 'SettingsController@update');
    Route::get('notifications', 'NotificationsController@index');
    Route::post('notifications', 'NotificationsController@store');




    Route::post('groups/data', 'GroupsController@data');
    Route::post('locations/data', 'LocationsController@data');
    Route::post('our_locations/data', 'OurLocationsController@data');
    Route::post('pilgrims_buses/data', 'PilgrimsBusesController@data');
    Route::post('managers/data', 'ManagersController@data');
    Route::post('pilgrims_class/data', 'PilgrimsClassController@data');
    Route::post('news/data', 'NewsController@data');
    Route::post('communication_guides/data', 'CommunicationGuidesController@data');
    Route::post('communication_guides_supervisors/data', 'CommunicationGuideSupervisorsController@data');
    Route::post('supervisors_jobs/data', 'SupervisorsJobsController@data');
    Route::post('tents/data', 'TentsController@data');

 
   
    

    Route::post('admins/data', 'AdminsController@data');
   

    Route::post('contact_messages/data', 'ContactMessagesController@data');

    Route::post('categories/data', 'CategoriesController@data');
 




    $this->get('login', 'LoginController@showLoginForm')->name('admin.login');
    $this->post('login', 'LoginController@login')->name('admin.login.submit');
    $this->get('logout', 'LoginController@logout')->name('admin.logout');
});
//});

