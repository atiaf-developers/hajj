@extends('layouts.backend')

@section('pageTitle',_lang('app.add'))

@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{url('admin/manual_accommodation')}}">{{_lang('app.manual_accommodation')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.add')}}</span></li>

@endsection

@section('css')
<link href="{{url('public/backend/css')}}/wizard.css" rel="stylesheet" type="text/css" />
@endsection
@section('js')
<script src="{{url('public/backend/js')}}/manual_accommodation.js" type="text/javascript"></script>
@endsection
@section('content')


<form role="form"  id="addEditManualAccommodationForm" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="col-md-9 col-md-offset-2">
        <div class="form-group form-md-line-input">
            <label class="col-md-2 control-label" for="form_control_1">{{_lang('app.type_of_accommodation')}}</label>
            <div class="col-md-10">

                <div class="md-radio col-md-3">
                    <input type="radio" id="suites_accommodation" name="type_of_accommodation" class="md-radiobtn" value="1" checked>
                    <label for="suites_accommodation">
                        <span></span>
                        <span class="check"></span>
                        <span class="box"></span>
                        {{_lang('app.suites_accommodation')}}</label>
                </div>
                <div class="md-radio has-info col-md-3">
                    <input type="radio" id="buildings_accommodation" name="type_of_accommodation" class="md-radiobtn" value="2" >
                    <label for="buildings_accommodation">
                        <span></span>
                        <span class="check"></span>
                        <span class="box"></span>
                        {{_lang('app.buildings_accommodation')}} </label>
                </div>
                <div class="md-radio has-warning col-md-3">
                    <input type="radio" id="tents_accommodation" name="type_of_accommodation" class="md-radiobtn" value="3">
                    <label for="tents_accommodation">
                        <span></span>
                        <span class="check"></span>
                        <span class="box"></span>
                        {{_lang('app.tents_accommodation')}} </label>
                </div>
                <div class="md-radio has-info col-md-3">
                    <input type="radio" id="buses_accommodation" name="type_of_accommodation" class="md-radiobtn" value="4">
                    <label for="buses_accommodation">
                        <span></span>
                        <span class="check"></span>
                        <span class="box"></span>
                        {{_lang('app.buses_accommodation')}} </label>
                </div>

            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-9 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-body">
                    <div class="col-md-6 col-md-offset-3">
                        <div class="form-group form-md-line-input">
                            <input type="text" class="form-control" id="pilgrim_code" name="pilgrim_code" value="">
                            <label for="code">{{_lang('app.pilgrim_code') }}</label>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="col-md-6 col-md-offset-3">
                        <div class="suite-item">
                            <div class="form-group form-md-line-input">
                                <select class="form-control" name="suites_accommodation_type">
                                    @foreach($suites_accommodation_types as $key=> $type)
                                    @if(in_array($key,[0,1,2,3]))
                                    <option  value="{{$key}}">{{ str_replace(' ','-',_lang('app.'.$type)) }}</option>
                                    @endif
                                    @endforeach
                                </select>
                                <label for="suites_accommodation_type">{{_lang('app.way_of_accommodation') }}</label>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="building-item" style="display:none;">
                            <div class="form-group form-md-line-input">
                                <select class="form-control" name="buildings_accommodation_type">
                                    @foreach($buildings_accommodation_types as $key=> $type)
                                    @if(in_array($key,[0,1,2,3]))
                                    <option  value="{{$key}}">{{ str_replace(' ','-',_lang('app.'.$type)) }}</option>
                                    @endif
                                    @endforeach
                                </select>
                                <label for="buildings_accommodation_type">{{_lang('app.way_of_accommodation') }}</label>
                                <span class="help-block"></span>
                            </div>
                        </div>
                    </div>
                    <div id="data-box" class="col-md-6 col-md-offset-3" style="position:relative;">
                        <div class="loading" style="position: absolute; z-index: 1000; top: 0; left: 0; height: 100%; width: 100%;background-color: rgba(224, 224, 224, 0.1);">
                            <div class="loader" style="display:none;width: 15px; height: 15px; position: absolute; left: 50%; top: 50%; margin-left: -15px; margin-top: -15px;">
                                <i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>
                            </div>
                        </div>

                        <div class="suite-item">
                            <div class="form-group form-md-line-input">
                                <select class="form-control" id="suite" name="suite">
                                    <option  value="">{{ _lang('app.choose') }}</option>
                                </select>
                                <label for = "suite">{{_lang('app.suite')}}</label>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="suite-item">
                            <div class="form-group form-md-line-input">
                                <select class="form-control" id="lounge" name="lounge">
                                    <option  value="">{{ _lang('app.choose') }}</option>

                                </select>
                                <label for = "lounge">{{_lang('app.lounge')}}</label>
                                <span class="help-block"></span>
                            </div>
                        </div>

                        <div class="building-item" style="display:none;">
                            <div class="form-group form-md-line-input">
                                <select class="form-control" id="building" name="building">
                                    <option  value="">{{ _lang('app.choose') }}</option>
                                </select>
                                <label for = "building">{{_lang('app.building')}}</label>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="building-item" style="display:none;">
                            <div class="form-group form-md-line-input">
                                <select class="form-control" id="floor" name="floor">
                                    <option  value="">{{ _lang('app.choose') }}</option>

                                </select>
                                <label for = "floor">{{_lang('app.floor')}}</label>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="building-item" style="display:none;">
                            <div class="form-group form-md-line-input">
                                <select class="form-control" id="room" name="room">
                                    <option  value="">{{ _lang('app.choose') }}</option>

                                </select>
                                <label for = "room">{{_lang('app.room')}}</label>
                                <span class="help-block"></span>
                            </div>
                        </div>

                        <div class="tent-item" style="display:none;">
                            <div class="form-group form-md-line-input">
                                <select class="form-control" id="tent" name="tent">
                                    <option  value="">{{ _lang('app.choose') }}</option>

                                </select>
                                <label for = "tent">{{_lang('app.tent')}}</label>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="bus-item" style="display:none;">
                            <div class="form-group form-md-line-input">
                                <select class="form-control" id="bus" name="bus">
                                    <option  value="">{{ _lang('app.choose') }}</option>

                                </select>
                                <label for = "bus">{{_lang('app.bus')}}</label>
                                <span class="help-block"></span>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
            <div class="panel-footer text-center">
                <button type="button" class="btn btn-info submit-form"
                        >{{_lang('app.save') }}</button>
            </div>


        </div>
    </div>




</form>
<script>
var new_lang = {

};
var new_config = {
    action: 'add'
};
</script>
@endsection