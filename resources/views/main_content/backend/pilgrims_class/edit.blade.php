@extends('layouts.backend')

@section('pageTitle',_lang('app.edit_pilgrims_class'))

@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{route('pilgrims_class.index')}}">{{_lang('app.pilgrims_class')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.edit')}}</span></li>
@endsection


@section('js')
<script src="{{url('public/backend/js')}}/pilgrims_class.js" type="text/javascript"></script>
@endsection
@section('content')
<form role="form"  id="addEditPilgrimsClassForm" enctype="multipart/form-data">
    {{ csrf_field() }}

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.basic_info') }}</h3>
        </div>
        <div class="panel-body">


            <div class="form-body">
                <input type="hidden" name="id" id="id" value="{{ $pilgrim_class->id }}">

                @foreach ($languages as $key => $value)

                <div class="form-group form-md-line-input col-md-4">
                    <input type="text" class="form-control" id="title[{{ $key }}]" name="title[{{ $key }}]" value="{{ $pilgrim_class_translations["$key"] }}">
                    <label for="title">{{_lang('app.title') }} {{ _lang('app. '.$key.'') }}</label>
                    <span class="help-block"></span>
                </div>

                @endforeach
                <div class="form-group form-md-line-input col-md-3">
                    <input type="number" class="form-control" id="this_order" name="this_order" value="{{ $pilgrim_class->this_order }}">
                    <label for="this_order">{{_lang('app.this_order') }}</label>
                    <span class="help-block"></span>
                </div>

            </div>
        </div>


    </div>


    <div class="panel panel-default">

        <div class="panel-body">


            <div class="form-body">
               

         

            </div>
        </div>


    </div>


    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.supervisor_info') }}</h3>
        </div>
        <div class="panel-body">

            <div class="form-body">
                <div class="form-group col-md-6">
                    <label class="control-label">{{_lang('app.image')}}</label>

                    <div class="supervisor_image_box">
                        <img src="{{url('public/uploads/supervisors').'/'.$pilgrim_class->supervisor_image}}" width="100" height="80" class="supervisor_image" />
                    </div>
                    <input type="file" name="supervisor_image" id="supervisor_image" style="display:none;">     
                    <span class="help-block"></span>             
                </div>



                <div class="form-group form-md-line-input col-md-6">
                    <input type="text" class="form-control" id="supervisor_name" name="supervisor_name" value="{{ $pilgrim_class->name }}">
                    <label for="name">{{_lang('app.supervisor_name') }}</label>
                    <span class="help-block"></span>
                </div>


                <div class="form-group form-md-line-input col-md-6">
                    <input type="text" class="form-control" id="supervisor_contact_numbers" name="supervisor_contact_numbers" value="{{ $pilgrim_class->contact_numbers }}" placeholder="+966663635,+96651515156,....">
                    <label for="supervisor_contact_numbers">{{_lang('app.supervisor_contact_numbers') }}</label>
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