@extends('layouts.backend')

@section('pageTitle',_lang('app.notifications'))


@section('js')
<script src="{{url('public/backend/js')}}/notifications.js" type="text/javascript"></script>
@endsection
@section('content')
<form role="form"  id="notificationsForm"  enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="panel panel-default" id="editSiteSettings">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.notifications') }}</h3>
        </div>
        <div class="panel-body">


            <div class="form-body">


                <div class="form-group form-md-line-input col-md-6">
                    <input type="text" class="form-control" id="title" name="title">
                    <label for="title">{{_lang('app.title') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-6">
                    <textarea rows="5" class="form-control" name="body" id="body" maxlength="200"></textarea>
                    <label for="body">{{_lang('app.body') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class = "form-group form-md-line-input col-md-4">
                    <select class = "form-control edited" id = "type" name = "type">
                        <option value = "0">{{_lang('app.general')}}</option>
                        <option value = "1">{{_lang('app.managers')}}</option>
                        <option value = "2">{{_lang('app.supervisors')}}</option>
                        <option value = "3">{{_lang('app.pilgrims')}}</option>
                    </select>
                    <label for="type">{{_lang('app.type')}}</label>
                    <span class="help-block"></span>
                </div>
                <div class="clearfix"></div>




            </div>




            <!--Table Wrapper Finish-->
        </div>
        <div class="panel-footer text-center">
            <button type="button" class="btn btn-info submit-form"
                    >{{_lang('app.save') }}</button>
        </div>

    </div>




</form>
@endsection