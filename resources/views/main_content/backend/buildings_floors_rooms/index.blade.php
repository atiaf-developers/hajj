@extends('layouts.backend')

@section('pageTitle', _lang('app.rooms'))
@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{url('admin/buildings')}}">{{_lang('app.buildings')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{url('admin/buildings_floors?building='.$floor->building_id)}}">{{_lang('app.floors')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{$floor->number}}</span></li>

@endsection
@section('js')
<script src="{{url('public/backend/js')}}/buildings_floors_rooms.js" type="text/javascript"></script>
@endsection
@section('content')
<div class="modal fade" id="addEditBuildingsFloorsRooms" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="addEditBuildingsFloorsRoomsLabel"></h4>
            </div>

            <div class="modal-body">


                <form role="form"  id="addEditBuildingsFloorsRoomsForm"  enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="id" value="0">
                    <div class="form-body">
                        <div class="form-group form-md-line-input col-md-12">
                            <input type="number" class="form-control" id="number" name="number" value="">
                            <label for="number">{{ _lang('app.number') }}</label>
                            <span class="help-block"></span>
                        </div>
                        <div class="form-group form-md-line-input col-md-12">
                            <input type="number" class="form-control" id="available_of_accommodation" name="available_of_accommodation" value="">
                            <label for="available_of_accommodation">{{ _lang('app.available_of_accommodation') }}</label>
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
                        <a class="btn green" style="margin-bottom: 40px;" href="" onclick="BuildingsFloorsRooms.add(this);return false;">{{ _lang('app.add_new')}}<i class="fa fa-plus"></i> </a>
                    </div>
                </div>
            </div>
        </div>



        <table class = "table table-striped table-bordered table-hover table-checkable order-column dataTable no-footer">
            <thead>
                <tr>
                    <th>{{_lang('app.room_no')}}</th>
                    <th>{{_lang('app.available_of_accommodation')}}</th>
                    <th>{{_lang('app.remaining')}}</th>
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

    };
    var new_config = {
        floor_id:"{{$floor->id}}"
    };
</script>
@endsection