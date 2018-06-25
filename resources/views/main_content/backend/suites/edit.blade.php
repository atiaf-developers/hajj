@extends('layouts.backend')

@section('pageTitle',_lang('app.suites'))
@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{url('admin/suites')}}">{{_lang('app.suites')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.edit')}}</span></li>

@endsection

@section('js')
<script src="{{url('public/backend/js')}}/suites.js" type="text/javascript"></script>
@endsection
@section('content')
<form role="form"  id="addEditSuitesForm" enctype="multipart/form-data">
    {{ csrf_field() }}

    <div class="panel panel-default" id="addEditRateQuestions">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.basic_info') }}</h3>
        </div>
        <div class="panel-body">


            <div class="form-body">
                <input type="hidden" name="id" id="id" value="{{$suite->id}}">
                <div class="row">

                    <div class="col-md-4">
                        <div class="form-group form-md-line-input col-md-12">
                            <input type="number" class="form-control" id="suite_number" name="suite_number" value="{{$suite->number}}">
                            <label for="suite_number">{{ _lang('app.suite_number') }}</label>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group form-md-line-input col-md-12">
                            <input type="number" class="form-control" id="this_order" name="this_order" value="{{$suite->this_order}}">
                            <label for="this_order">{{ _lang('app.this_order') }}</label>
                            <span class="help-block"></span>
                        </div>
                    </div>
                </div>
            </div>
            <!--Table Wrapper Finish-->
        </div>

    </div>


    <div class="panel panel-default">

        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.lounges') }}</h3>
        </div>
        <div class="panel-body">
            <a class="btn btn-primary add-lounge">{{_lang('app.add')}}</a>
            <div class="form-body">

                <div class="table-scrollable" style="border:none;">
                    <table class="table" id="lounge-table">
                        <tbody>            
                            @foreach($suite->lounges as $key=> $one)
                            <tr class="lounge-one">
                                <td>
                                    <div class="form-group form-md-line-input">
                                        <input type="number" class="form-control" name="lounge_number[{{$key}}]" value="{{$one->number}}">
                                         <label for="">{{_lang('app.lounge_number') }}</label>
                                        <span class="help-block"></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group form-md-line-input">
                                        <input type="number" class="form-control" name="available_of_accommodation[{{$key}}]" value="{{$one->available_of_accommodation}}" aria-required="true">
                                         <label for="">{{_lang('app.available_of_accommodation') }}</label>
                                        <span class="help-block"></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group form-md-line-input">
                                        <select class="form-control edited" name="gender[{{$key}}]">
                                            <option  value="">{{ _lang('app.choose') }}</option>
                                            <option {{$one->gender==1?'selected':''}}  value="1">{{ _lang('app.male') }}</option>
                                            <option {{$one->gender==2?'selected':''}}  value="2">{{ _lang('app.female') }}</option>
                                        </select>
                                         <label for="">{{_lang('app.gender') }}</label>
                                        <span class="help-block"></span>
                                    </div>
                                </td>
                                @if(!$loop->first)
                                <td><a class="btn btn-danger remove-lounge" onclick="Suites.delete_lounge(this);return false;" data-id="{{$one->id}}">{{_lang('app.remove')}}</a></td>
                                @else
                                  <td></td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>




            </div>

            <!--Table Wrapper Finish-->
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
    'action':'edit'
};

</script>
@endsection