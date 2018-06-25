@extends('layouts.backend')

@section('pageTitle', _lang('app.communication_guide_supervisors'))
@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{route('communication_guides.index')}}">{{_lang('app.communication_guides')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{ $communication_guide->title }}</span><i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.communication_guide_supervisors')}}</span></li>

@endsection
@section('js')
<script src="{{url('public/backend/js')}}/communication_guide_supervisors.js" type="text/javascript"></script>
@endsection
@section('content')
<div class="modal fade" id="addEditCommunicationGuideSupervisors" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="addEditCommunicationGuideSupervisorsLabel"></h4>
            </div>

            <div class="modal-body">


                <form role="form"  id="addEditCommunicationGuideSupervisorsForm"  enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="id" value="0">
                    <div class="form-body">
                        <div class="form-group form-md-line-input">
                            <input type="text" class="form-control" id="name" name="name" placeholder="{{_lang('app.name')}}">
                            <label for="name">{{_lang('app.name')}}</label>
                            <span class="help-block"></span>
                        </div>

                        <div class="form-group form-md-line-input">
                            <input type="text" class="form-control" id="contact_numbers" name="contact_numbers" placeholder="+96665216516,+96654654654,....">
                            <label for="contact_numbers">{{_lang('app.contact_numbers')}}</label>
                            <span class="help-block"></span>
                        </div>

                        <div class="form-group form-md-line-input ">
                            <select class="form-control edited" id="job" name="job">
                                @foreach ($supervisors_jobs as $job)
                                    <option  value="{{ $job->id }}">{{ $job->title }}</option>
                                @endforeach
                                
                               
                            </select>
                             <label for="job">{{_lang('app.job') }}</label>
                            <span class="help-block"></span>
                       </div> 
                        
                      

                        <div class="form-group form-md-line-input">
                            <label class="control-label">{{_lang('app.image')}}</label>
                            <div class="image_box">
                                <img src="{{url('no-image.png')}}" width="100" height="80" class="image" />
                            </div>
                            <input type="file" name="image" id="image" style="display:none;">     
                            <span class="help-block"></span>             
                        </div>


                    </div>


                </form>

            </div>

            <div class = "modal-footer">
                <span class = "margin-right-10 loading hide"><i class = "fa fa-spin fa-spinner"></i></span>
                <button type = "button" class = "btn btn-info submit-form"
                        >{{_lang("app.save")}}</button>
                <button type = "button" class = "btn btn-white"
                        data-dismiss = "modal">{{_lang("app.close")}}</button>
            </div>
        </div>
    </div>
</div>
<div class = "panel panel-default">

    <div class = "panel-body">
        <!--Table Wrapper Start-->
        <div class="table-toolbar">
            <div class="row">
                <div class="col-md-6">
                    <div class="btn-group">
                        <a class="btn green" style="margin-bottom: 40px;" href="" onclick="CommunicationGuideSupervisors.add(); return false;">{{ _lang('app.add_new')}}<i class="fa fa-plus"></i> </a>
                    </div>
                </div>
            </div>
        </div>

        <table class = "table table-striped table-bordered table-hover table-checkable order-column dataTable no-footer">
            <thead>
                <tr>
                    <th>{{_lang('app.name')}}</th>
                    <th>{{_lang('app.image')}}</th>
                    <th>{{_lang('app.options')}}</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>

        <!--Table Wrapper Finish-->
    </div>
</div>
<script>
    var new_lang = {
      communication_guide: "{!! $communication_guide->id !!}"
    };
</script>
@endsection