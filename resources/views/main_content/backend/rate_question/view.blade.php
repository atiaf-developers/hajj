@extends('layouts.backend')

@section('pageTitle', _lang('app.pilgrims'))
@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{url('admin/pilgrims')}}">{{_lang('app.pilgrims')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.view')}}</span></li>
@endsection
@section('js')
<!--<script src="{{url('public/backend/js')}}/pilgrims.js" type="text/javascript"></script>-->
@endsection
@section('content')
<div class="container">
    <div class="row">

        <h2 class="text-center"></h2>

        <div class="col-md-12">
            <!-- BEGIN SAMPLE TABLE PORTLET-->
            <div class="portlet box red">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-cogs"></i>{{_lang('app.info')}}
                    </div>
                    <div class="tools">


                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-hover text-center">

                            <tbody>
                                <tr>
                                    <td>{{ _lang('app.question')}}</td>
                                    <td>{{$question->title}}</td>

                                </tr>
                                
                                










                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- END SAMPLE TABLE PORTLET-->
        </div>




    </div>
</div>
<script>
    var new_lang = {

    };

</script>
@endsection
