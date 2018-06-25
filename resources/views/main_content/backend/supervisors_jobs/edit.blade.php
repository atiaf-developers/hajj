@extends('layouts.backend')

@section('pageTitle',_lang('app.edit_supervisor_job'))

@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{route('supervisors_jobs.index')}}">{{_lang('app.supervisor_jobs')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.edit_supervisor_job')}}</span></li>
@endsection

@section('js')


<script src="{{url('public/backend/js')}}/supervisor_jobs.js" type="text/javascript"></script>
@endsection
@section('content')
<form role="form"  id="addEditSupervisorJobsForm" enctype="multipart/form-data">
    {{ csrf_field() }}

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.job_info') }}</h3>
        </div>
        <div class="panel-body">


            <div class="form-body">
                <input type="hidden" name="id" id="id" value="{{ $supervisor_job->id }}">
                @foreach ($languages as $key => $value)
               
                    <div class="form-group form-md-line-input col-md-6">
                            <input type="text" class="form-control" id="title[{{ $key }}]" name="title[{{ $key }}]" value="{{ $supervisor_job_translations[$key] }}">
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
                        <option {{ $supervisor_job->active == 1 ? 'selected' : '' }} value="1">{{ _lang('app.active') }}</option>
                        <option {{ $supervisor_job->active == 0 ? 'selected' : '' }}  value="0">{{ _lang('app.not_active') }}</option>
                    </select>
                     <label for="status">{{_lang('app.status') }}</label>
                    <span class="help-block"></span>
                </div> 

                <div class="form-group form-md-line-input col-md-6">
                    <input type="number" class="form-control" id="this_order" name="this_order" value="{{ $supervisor_job->this_order }}">
                    <label for="this_order">{{_lang('app.this_order') }}</label>
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