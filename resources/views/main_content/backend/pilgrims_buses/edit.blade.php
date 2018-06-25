@extends('layouts.backend')

@section('pageTitle',_lang('app.edit_bus'))

@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{route('pilgrims_buses.index')}}">{{_lang('app.pligrims_buses')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.edit_bus')}}</span></li>
@endsection

@section('js')

<script src="{{url('public/backend/js')}}/pilgrims_buses.js" type="text/javascript"></script>
@endsection
@section('content')
<form role="form"  id="addEditPilgrimsBusesForm" enctype="multipart/form-data">
    {{ csrf_field() }}

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.bus_info') }}</h3>
        </div>
        <div class="panel-body">


            <div class="form-body">

                <input type="hidden" name="id" id="id" value="{{ $pilgrims_bus->id }}">

                <div class="form-group form-md-line-input col-md-6">
                    <select class="form-control" id="location" name="location">
                        <option value = "" selected>{{_lang('app.choose')}}</option>
                        @foreach ($locations as $key => $value)
                        <option {{ $pilgrims_bus->location_id == $value->id ? 'selected' : '' }} value = "{{$value->id}}">{{$value->title}}</option>
                        @endforeach
                    </select>
                    <label for = "location">{{_lang('app.location')}}</label>
                    <span class="help-block"></span>  
                </div>

                <div class="form-group form-md-line-input col-md-4">
                    <input type="number" class="form-control" id="bus_number" name="bus_number" value="{{ $pilgrims_bus->bus_number }}">
                    <label for="title">{{_lang('app.bus_number') }}</label>
                    <span class="help-block"></span>
                </div>

                <div class="form-group form-md-line-input col-md-4">
                    <input type="number" class="form-control" id="num_of_seats" name="num_of_seats" value="{{ $pilgrims_bus->num_of_seats }}">
                    <label for="title">{{_lang('app.num_of_seats') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-4">
                    <input type="number" class="form-control" id="this_order" name="this_order" value="{{ $pilgrims_bus->this_order }}">
                    <label for="this_order">{{_lang('app.this_order') }}</label>
                    <span class="help-block"></span>
                </div>




            </div>
        </div>


    </div>


    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{ _lang('app.supervisor_info') }}</h3>
        </div>
        <div class="panel-body">

            <div class="form-body">


                <div class="form-group form-md-line-input col-md-4">
                    <input type="text" class="form-control" id="supervisor_name" name="supervisor_name" value="{{ $pilgrims_bus->name }}">
                    <label for="name">{{_lang('app.supervisor_name') }}</label>
                    <span class="help-block"></span>
                </div>

                <div class="form-group form-md-line-input col-md-4">
                    <input type="text" class="form-control" id="supervisor_username" name="supervisor_username" value="{{ $pilgrims_bus->username }}">
                    <label for="name">{{_lang('app.supervisor_username') }}</label>
                    <span class="help-block"></span>
                </div>


                <div class="form-group form-md-line-input col-md-4">
                    <input type="password" class="form-control" id="supervisor_password" name="supervisor_password" value="">
                    <label for="name">{{_lang('app.supervisor_password') }}</label>
                    <span class="help-block"></span>
                </div>


                <div class="form-group form-md-line-input col-md-5">
                    <input type="text" class="form-control" id="supervisor_contact_numbers" name="supervisor_contact_numbers" value="{{ $pilgrims_bus->contact_numbers }}" placeholder="+966663635,+96651515156,....">
                    <label for="supervisor_contact_numbers">{{_lang('app.supervisor_contact_numbers') }}</label>
                    <span class="help-block"></span>
                </div>

                <div class="form-group form-md-line-input col-md-2">
                    <select class="form-control edited" id="active" name="active">
                        <option {{ $pilgrims_bus->active == 1 ? 'selected' : '' }} value="1">{{ _lang('app.active') }}</option>
                        <option {{ $pilgrims_bus->active == 0 ? 'selected' : '' }} value="0">{{ _lang('app.not_active') }}</option>
                    </select>
                    <label for="status">{{_lang('app.status') }}</label>
                    <span class="help-block"></span>
                </div> 

                <div class="form-group col-md-6">
                    <label class="control-label">{{_lang('app.image')}}</label>

                    <div class="supervisor_image_box">
                        <img src="{{url('public/uploads/supervisors').'/'.$pilgrims_bus->supervisor_image}}" width="100" height="80" class="supervisor_image" />
                    </div>
                    <input type="file" name="supervisor_image" id="supervisor_image" style="display:none;">     
                    <span class="help-block"></span>             
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