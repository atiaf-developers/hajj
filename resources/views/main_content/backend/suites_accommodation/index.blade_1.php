@extends('layouts.backend')

@section('pageTitle',_lang('app.suites_accommodation'))

@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.suites_accommodation')}}</span></li>

@endsection

@section('js')
<script src="{{url('public/backend/js')}}/suites_accommodation.js" type="text/javascript"></script>
@endsection
@section('content')
<form role="form"  id="editSettingsForm"  enctype="multipart/form-data">
    {{ csrf_field() }}



    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.accommodation') }}</h3>
        </div>
        <div class="panel-body">

            <div class="form-body">
                <div class="form-group form-md-line-input col-md-3">
                    <select class="form-control" id="location" name="location">

                        @foreach ($locations as $one)
                        <option value = "{{$one->id}}">{{$one->title}}</option>
                        @endforeach
                    </select>
                    <label for = "location">{{_lang('app.locations')}}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-3">
                    <select class="form-control" id="pilgrim_class" name="pilgrim_class">

                        @foreach ($pilgrims_class as $one)
                        <option value = "{{$one->id}}">{{$one->title}}</option>
                        @endforeach
                    </select>
                    <label for = "pilgrim_class">{{_lang('app.pilgrims_class')}}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-3">
                    <select class="form-control" name="gender">
                        <option  value="">{{ _lang('app.choose') }}</option>
                        <option  value="1">{{ _lang('app.male') }}</option>
                        <option  value="2">{{ _lang('app.female') }}</option>
                    </select>
                    <label for="">{{_lang('app.gender') }}</label>
                    <span class="help-block"></span>
                </div>



            </div>




            <!--Table Wrapper Finish-->
        </div>
        <div class="panel-footer text-center">
            <button type="button" class="btn btn-info submit-form"
                    >{{_lang('app.apply') }}</button>
        </div>
    </div>






</form>
@endsection