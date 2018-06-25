@extends('layouts.backend')

@section('pageTitle',_lang('app.communication_guides'))
@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{route('communication_guides.index')}}">{{_lang('app.communication_guides')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.edit')}}</span></li>

@endsection

@section('js')
<script src="{{url('public/backend/js')}}/communication_guides.js" type="text/javascript"></script>
@endsection
@section('content')
<form role="form"  id="addEditCommunicationGuidesForm" enctype="multipart/form-data">
    {{ csrf_field() }}

    <div class="panel panel-default" id="addEditCommunicationGuides">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.basic_info') }}</h3>
        </div>
        <div class="panel-body">


            <div class="form-body">

                <input type="hidden" name="id" id="id" value="{{ $communication_guide->id }}">
                

                    @foreach ($languages as $key => $value)
                    
                         <div class="form-group form-md-line-input col-md-6">

                                <input type="text" class="form-control" id="title[{{ $key }}]" name="title[{{ $key }}]" value="{{ $title_translations["$key"] }}">
                                <label for="title">{{_lang('app.title') }} {{ _lang('app. '.$key.'') }}</label>
                                <span class="help-block"></span>

                        </div>

                        <div class="form-group form-md-line-input col-md-6">

                                <input type="text" class="form-control" id="description[{{ $key }}]" name="description[{{ $key }}]" value="{{ $description_translations["$key"] }}">
                                <label for="description">{{_lang('app.description') }} {{ _lang('app. '.$key.'') }}</label>
                                <span class="help-block"></span>

                        </div>
                    
                    @endforeach
            </div>
            <!--Table Wrapper Finish-->
        </div>
        
    </div>
    <div class="panel panel-default">

        <div class="panel-body">


            <div class="form-body">

                <div class="form-group col-md-2">
                    <label class="control-label">{{_lang('app.image')}}</label>
                      <div class="image_box">
                          @if ($communication_guide->image)
                               <img src="{{url('public/uploads/communication_guides').'/'.$communication_guide->image}}" width="100" height="80" class="image" />
                          @else
                               <img src="{{url('no-image.png')}}" width="100" height="80" class="image" />
                          @endif
                    </div>
                    <input type="file" name="image" id="image" style="display:none;">     
                    <span class="help-block"></span>             
                </div>


                 <div class="form-group form-md-line-input col-md-2">
                    <input type="number" class="form-control" id="this_order" name="this_order" value="{{ $communication_guide->this_order }}">
                    <label for="this_order">{{_lang('app.this_order') }}</label>
                    <span class="help-block"></span>
                </div>

                <div class="form-group form-md-line-input col-md-2">
                    <select class="form-control edited" id="active" name="active">
                    <option {{ $communication_guide->active == 1 ? 'selected' : '' }} value="1">{{ _lang('app.active') }}</option>
                    <option {{ $communication_guide->active == 0 ? 'selected' : '' }}  value="0">{{ _lang('app.not_active') }}</option>
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