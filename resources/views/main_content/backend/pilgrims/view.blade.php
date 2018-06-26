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
                                    <td>{{ _lang('app.name')}}</td>
                                    <td>{{$pilgrim->name}}</td>

                                </tr>
                                <tr>
                                    <td>{{ _lang('app.mobile')}}</td>
                                    <td>{{$pilgrim->mobile}}</td>

                                </tr>
                                <tr>
                                    <td>{{ _lang('app.nationality')}}</td>
                                    <td>{{$pilgrim->nationality}}</td>

                                </tr>
                                <tr>
                                    <td>{{ _lang('app.reservation_no')}}</td>
                                    <td>{{$pilgrim->reservation_no}}</td>

                                </tr>
                                <tr>
                                    <td>{{ _lang('app.gender')}}</td>
                                    <td>{{$pilgrim->gender==1?_lang('app.male'):_lang('app.female')}}</td>

                                </tr>
                                <tr>
                                    <td>{{ _lang('app.mobile')}}</td>
                                    <td>{{$pilgrim->mobile}}</td>

                                </tr>
                                <tr>
                                    <td>{{ _lang('app.city')}}</td>
                                    <td>{{$pilgrim->location_title}}</td>

                                </tr>
                                <tr>
                                    <td>{{ _lang('app.ssn')}}</td>
                                    <td>{{$pilgrim->ssn}}</td>

                                </tr>
                                <tr>
                                    <td>{{ _lang('app.code')}}</td>
                                    <td>{{$pilgrim->code}}</td>

                                </tr>
                                @if($pilgrim->bus_number)
                                <tr>
                                    <td>{{ _lang('app.bus_number')}}</td>
                                    <td>{{$pilgrim->bus_number}}</td>

                                </tr>
                                @endif
                                @if(count($pilgrim->accommodation)>0)
                                @foreach($pilgrim->accommodation as $one)
                                <tr>
                                    <td>{{ _lang('app.'.$one['name'],$lang_code)}}</td>
                                    <td>
                                        {{ $one['value']}}
                                    </td>

                                </tr>

                                @endforeach
                                @endif


                                <tr>
                                    <td>{{ _lang('app.status')}}</td>
                                    <td>
                                        @php
                                        if ($pilgrim->active==1) {
                                        $class = 'label-success';
                                        $message = _lang('app.active');
                                        } else {
                                        $class = 'label-danger';
                                        $message = _lang('app.not_active');
                                        }
                                        @endphp
                                        <span class="label label-sm {{ $class}}">
                                            {{$message}} </span>
                                    </td>
                                </tr>


                                <tr>
                                    <td>{{ _lang('app.image')}}</td>
                                    <td>
                                        <a class="fancybox-button" data-rel="fancybox-button" href="{{$pilgrim->image}}">
                                            <img alt="" style="width:120px;height: 120px;" src="{{$pilgrim->image}}">
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ _lang('app.card')}}</td>
                                    <td>
                                        <a href="{{url('admin/pilgrims/'.$pilgrim->id.'/download-card')}}" class="btn btn-default">
                                            {{_lang('app.download')}}
                                        </a>
                                    </td>
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
