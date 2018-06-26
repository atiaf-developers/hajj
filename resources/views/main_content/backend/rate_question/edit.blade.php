@extends('layouts.backend')

@section('pageTitle',_lang('app.rate_question'))
@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{url('admin/rate_question')}}">{{_lang('app.rate_question')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.add_rate_question')}}</span></li>

@endsection

@section('js')
<script src="{{url('public/backend/js')}}/rate_question.js" type="text/javascript"></script>
@endsection
@section('content')
<form role="form"  id="addEditRateQuestionsForm" enctype="multipart/form-data">
    {{ csrf_field() }}

    <div class="panel panel-default" id="addEditRateQuestions">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.basic_info') }}</h3>
        </div>
        <div class="panel-body">


            <div class="form-body">
                <input type="hidden" name="id" id="id" value="{{ $question->id }}">
                <div class="row">

                    @foreach ($languages as $key => $value)
                    <div class="col-md-4">
                        <div class="form-group form-md-line-input col-md-12">
                            <input type="hidden" name="translations[{{ $key }}][id]" value="{{  isset($question->translations["$key"]->id)?$question->translations["$key"]->id:'' }}">
                            <input type="text" class="form-control" id="title[{{ $key }}]" name="translations[{{ $key }}][title]" value="{{  isset($question->translations["$key"]->title)?$question->translations["$key"]->title:'' }}">
                            <label for="title">{{ _lang('app.'.$value) }}</label>
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

        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.answers') }}</h3>
        </div>
        <div class="panel-body">
            <a class="btn btn-primary add-answer">{{_lang('app.add')}}</a>
            <div class="form-body">

                <div class="table-scrollable" style="border:none;">
                    <table class="table" id="answers-table">
                        <tbody>            
                            @php $count=0 @endphp
                            @foreach($answers as $answer)
                            <tr class="answer-one">
                                <td>
                                    <div class="form-group">
                                        <input type="hidden" name="answers[{{$count}}][id]" value="{{$answer->id}}">
                                        <input placeholder="order" style="width: 100px;" type="number" class="form-control form-filter input-lg"  name="answers[{{ $count }}][order]" value="{{ $answer->this_order }}">
                                        <span class="help-block"></span>
                                    </div>
                                </td>

                                @foreach ($languages as $key => $value)
                                <td>
                                    <div class="form-group">
                                         <input type="hidden" name="answers[{{ $count }}][translations][{{ $key }}][id]" value="{{  isset($answer->translations["$key"]->id)?$answer->translations["$key"]->id:'' }}">
                                        <input  placeholder="{{ $key }}" type="text" class="form-control form-filter input-lg"  name="answers[{{ $count }}][translations][{{ $key }}][title]" value="{{  isset($answer->translations["$key"]->title)?$answer->translations["$key"]->title:'' }}">

                                        <span class="help-block"></span>
                                    </div>
                                </td>
                                @endforeach
                                <td>
                                    <a class="btn btn btn-danger remove-answer">{{_lang('app.delete')}}</a>
                                </td>
                            </tr>
                            @php $count++ @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>




            </div>

            <!--Table Wrapper Finish-->
        </div>

    </div>


    <div class="panel panel-default">

        <div class="panel-body">


            <div class="form-body">
                <div class="form-group form-md-line-input col-md-6">
                    <input type="number" class="form-control" id="this_order" name="this_order" value="{{ $question->this_order }}">
                    <label for="this_order">{{_lang('app.this_order') }}</label>
                    <span class="help-block"></span>
                </div>

                <div class="form-group form-md-line-input col-md-6">
                    <select class="form-control edited" id="active" name="active">
                        <option {{ $question->active==1?'selected':'' }}  value="1">{{ _lang('app.active') }}</option>
                        <option {{ $question->active==0?'selected':'' }}  value="0">{{ _lang('app.not_active') }}</option>
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