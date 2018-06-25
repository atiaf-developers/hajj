@extends('layouts.backend')

@section('pageTitle',_lang('app.common_questions'))
@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{url('admin/common_questions/')}}">{{_lang('app.common_questions')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.edit_common_questions')}}</span></li>

@endsection

@section('js')
<script src="{{url('public/backend/js')}}/common_questions.js" type="text/javascript"></script>
@endsection
@section('content')
<form role="form"  id="addEditCommonQuestionsForm" enctype="multipart/form-data">
    {{ csrf_field() }}

    <div class="panel panel-default" id="addEditCommonQuestions">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.basic_info') }}</h3>
        </div>
        <div class="panel-body">


            <div class="form-body">
                <input type="hidden" name="id" id="id" value="{{$data->id}}">
                
                <div class="row">
                
                    @foreach ($languages as $key => $value)
                        <div class="col-md-4">
                            <div class="form-group form-md-line-input col-md-12">
                                <input type="text" class="form-control" id="question[{{ $key }}]" name="question[{{ $key }}]" value="{{ $questions[$key] }}">
                                <label for="question">{{_lang('app.question') }} {{ _lang('app. '.$value.'') }}</label>
                                <span class="help-block"></span>
                            </div>
                            <div class="form-group form-md-line-input col-md-12">
                                <textarea class="form-control" id="answer[{{ $key }}]" name="answer[{{ $key }}]"  cols="30" rows="10">{{ $answers[$key] }}</textarea>
                                <label for="answer">{{_lang('app.answer') }} {{ _lang('app. '.$value.'') }}</label>
                                <span class="help-block"></span>
                            </div>
                        </div>

                    @endforeach
               
                </div>

            </div>

            <!--Table Wrapper Finish-->
        </div>
   
    </div>
    <div class="panel panel-default">

        <div class="panel-body">


            <div class="form-body">
                 <div class="form-group form-md-line-input col-md-6">
                    <input type="number" class="form-control" id="this_order" name="this_order" value="{{$data->this_order}}">
                    <label for="this_order">{{_lang('app.this_order') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-6">
                    <select class="form-control edited" id="active" name="active">
                        <option {{$data->active==1?'selected':''}}  value="1">{{ _lang('app.active') }}</option>
                        <option {{$data->active==0?'selected':''}}  value="0">{{ _lang('app.not_active') }}</option>
                    </select>
                    <label for="status">{{_lang('app.status') }}</label>
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