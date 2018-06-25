@extends('layouts.backend')

@section('pageTitle',_lang('app.add_location'))

@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{route('our_locations.index')}}">{{_lang('app.our_locations')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.add_location')}}</span></li>
@endsection

@section('js')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDWYbhmg32SNq225SO1jRHA2Bj6ukgAQtA&libraries=places&language={{App::getLocale()}}"></script>

<script src="{{url('public/backend/js')}}/map.js" type="text/javascript"></script>

<script src="{{url('public/backend/js')}}/our_locations.js" type="text/javascript"></script>
@endsection
@section('content')
<form role="form"  id="addEditOurLocationsForm" enctype="multipart/form-data">
    {{ csrf_field() }}

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.location_info') }}</h3>
        </div>
        <div class="panel-body">


            <div class="form-body">
                <input type="hidden" name="id" id="id" value="0">
                @foreach ($languages as $key => $value)
               
                    <div class="form-group form-md-line-input col-md-6">
                            <input type="text" class="form-control" id="title[{{ $key }}]" name="title[{{ $key }}]" value="">
                            <label for="title">{{_lang('app.title') }} {{ _lang('app. '.$key.'') }}</label>
                            <span class="help-block"></span>
                    </div>

                @endforeach


           </div>
    </div>

       
    </div>


    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"></h3>
        </div>
        <div class="panel-body">


            <div class="form-body">
                <div class="form-group form-md-line-input col-md-3">
                    <select class="form-control edited" id="active" name="active">
                        <option  value="1">{{ _lang('app.active') }}</option>
                        <option  value="0">{{ _lang('app.not_active') }}</option>
                    </select>
                     <label for="status">{{_lang('app.status') }}</label>
                    <span class="help-block"></span>
                </div> 

                <div class="form-group form-md-line-input col-md-6">
                    <input type="number" class="form-control" id="this_order" name="this_order" value="">
                    <label for="this_order">{{_lang('app.this_order') }}</label>
                    <span class="help-block"></span>
                </div>
            
           </div>
    </div>

       
    </div>


    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"></h3>
        </div>
        <div class="panel-body">

                <div class="form-body">
                <div class="form-group col-md-6">
                    <label class="control-label">{{_lang('app.image')}}</label>

                    <div class="location_image_box">
                        <img src="{{url('no-image.png')}}" width="100" height="80" class="location_image" />
                    </div>
                    <input type="file" name="location_image" id="location_image" style="display:none;">     
                    <span class="help-block"></span>             
                </div>


                 <div class="form-group form-md-line-input col-md-6">
                    <input type="text" class="form-control" id="contact_numbers" name="contact_numbers" value="" placeholder="+966663635,+96651515156,....">
                    <label for="contact_numbers">{{_lang('app.contact_numbers') }}</label>
                    <span class="help-block"></span>
                </div>




                <input value="" type="hidden" id="lat" name="lat">
                <input value="" type="hidden" id="lng" name="lng">
                    <span class="help-block utbox"></span>
                <div class="maplarger">
                            <input id="pac-input" class="controls" type="text"
                                   placeholder="Enter a location">
                            <div id="map" style="height: 500px; width:100%;"></div>
                            <div id="infowindow-content">
                                <span id="place-name"  class="title"></span><br>
                                <span id="place-address"></span>
                            </div>
                </div>
                
               </div>
        </div>

        <div class="panel-footer text-center">
            <button type="button" class="btn btn-info submit-form"
                    >{{_lang('app.save') }}</button>
        </div>

       
    </div>


</form>
<script>
var new_lang = {

};
var new_config = {
  
};

</script>
@endsection