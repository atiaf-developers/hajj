@extends('layouts.backend')

@section('pageTitle', _lang('app.managers'))
@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.managers')}}</span></li>

@endsection
@section('js')
<script src="{{url('public/backend/js')}}/managers.js" type="text/javascript"></script>
@endsection
@section('content')
<div class="modal fade" id="addEditManagers" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="addEditManagersLabel"></h4>
            </div>

            <div class="modal-body">


                <form role="form"  id="addEditManagersForm"  enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="id" value="0">
                    <div class="form-body">
                        <div class="form-group form-md-line-input">
                            <input type="text" class="form-control" id="username" name="username" placeholder="{{_lang('app.username')}}">
                            <label for="username">{{_lang('app.username')}}</label>
                            <span class="help-block"></span>
                        </div>
                        <div class="form-group form-md-line-input">
                            <div class="input-group input-group-sm">
                                <div class="input-group-control">
                                    <input type="password" class="form-control input-sm" id="password" name="password" placeholder="{{_lang('app.password')}}">
                                    <label for="password">{{_lang('app.password')}}</label>
                                </div>
                                <span class="input-group-btn btn-right">
                                    <button class="btn green-haze" type="button" id="show-password">{{_lang('app.show')}}</button>
                                    <button class="btn green-haze" type="button" id="random-password">{{_lang('app.rondom')}}</button>
                                </span>
                            </div>
                            <span class="help-block"></span>
                        </div>
                      

                        <div class = "form-group form-md-line-input">
                            <select class = "form-control edited" id = "active" name = "active">
                                <option value = "1">{{_lang('app.active')}}</option>
                                <option value = "0">{{_lang('app.not_active')}}</option>

                            </select>

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
                        <a class="btn green" style="margin-bottom: 40px;" href="" onclick="Managers.add(); return false;">{{ _lang('app.add_new')}}<i class="fa fa-plus"></i> </a>
                    </div>
                </div>
            </div>
        </div>

        <table class = "table table-striped table-bordered table-hover table-checkable order-column dataTable no-footer">
            <thead>
                <tr>
                    <th>{{_lang('app.username')}}</th>
                    <th>{{_lang('app.active')}}</th>
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
        'add_manager': "{{_lang('app.add_manager')}}",
        'edit_manager': "{{_lang('app.edit_manager')}}",
        messages: {
            username: {
                required: lang.required
            },
        }
    };
</script>
@endsection