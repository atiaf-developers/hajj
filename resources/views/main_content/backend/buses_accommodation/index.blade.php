@extends('layouts.backend')

@section('pageTitle',_lang('app.buses_accommodation'))

@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.buses_accommodation')}}</span></li>

@endsection
@section('css')
<link href="{{url('public/backend/css')}}/wizard.css" rel="stylesheet" type="text/css" />
@endsection
@section('js')
<script src="{{url('public/backend/js')}}/buses_accommodation.js" type="text/javascript"></script>
@endsection
@section('content')
{{ csrf_field() }}
<form method="" id="filter-reports">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{ _lang('app.filter_by') }}</h3>
        </div>
        <div class="panel-body">

            <div class="row">
                <div class="form-group col-sm-4">
                    <label class="col-sm-3 inputbox utbox control-label">{{_lang('app.locations')}}</label>
                    <div class="col-sm-9 inputbox">
                        <select class="form-control" name="location" id="location">
                            <option value="">{{_lang('app.choose')}}</option>
                            @foreach($locations as $one)
                            <option {{ (isset($location) && $location==$one->id) ?'selected':''}} value="{{$one->id}}">{{$one->title}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group col-sm-4">
                    <label class="col-sm-3 inputbox utbox control-label">{{_lang('app.buses')}}</label>
                    <div class="col-sm-9 inputbox">
                        <select class="form-control" name="bus" id="bus">
                            <option value="">{{_lang('app.choose')}}</option>
                            @foreach($buses as $one)
                            <option {{ (isset($bus) && $bus==$one->id) ?'selected':''}} value="{{$one->id}}">{{$one->number}}</option>
                            @endforeach


                        </select>
                    </div>
                </div>
 
                <div class="form-group col-md-4 col-md-offset-1">
                    <label class="col-sm-4 inputbox utbox control-label">{{ _lang('app.code') }}</label>
                    <div class="col-sm-8 inputbox">

                        <input type="text" class="form-control" placeholder=""  name="code" value="{{ isset($code) ? $code :'' }}">

                    </div>
                </div>
                <div class="form-group col-md-4 col-md-offset-1">
                    <label class="col-sm-4 inputbox utbox control-label">{{ _lang('app.reservation_no') }}</label>
                    <div class="col-sm-8 inputbox">

                        <input type="text" class="form-control" placeholder=""  name="reservation_no" value="{{ isset($reservation_no) ? $reservation_no :'' }}">

                    </div>
                </div>
                <div class="form-group col-sm-4">
                    <label class="col-sm-3 inputbox utbox control-label">{{_lang('app.per_page')}}</label>
                    <div class="col-sm-9 inputbox">
                        <select class="form-control" name="per_page" id="per_page">
                            <option {{ (isset($per_page) && $per_page==10) ?'selected':''}}  value="10">10</option>
                            <option {{ (isset($per_page) && $per_page==50) ?'selected':''}} value="50">50</option>
                            <option {{ (isset($per_page) && $per_page==100) ?'selected':''}} value="100">100</option>


                        </select>
                    </div>
                </div>



            </div>
            <!--row-->
        </div>
        <div class="panel-footer text-center">
            <button class="btn btn-info submit-form btn-report" type="submit">{{ _lang('app.apply') }}</button>
        </div>
    </div>
</form>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="pull-left">
            <h3 class="panel-title">{{ _lang('app.search_results') }}</h3>
        </div>
        <div class="pull-right">
       
            <a href="{{ route('buses_accommodation.create') }}" class="btn btn-circle btn-default">
                <i class="fa fa-plus"></i> {{_lang('app.add_new')}} </a>
            
            <!--            <a href="javascript:;" class="btn btn-circle btn-default">
                            <i class="fa fa-pencil"></i> Add </a>-->

        </div>

        <div class="clearfix"></div>
    </div>
    <div class="panel-body">


        <div class="row">
            @if($pilgrims->count()>0)
            <div class="col-sm-12">
                <table class = "table table-responsive table-striped table-bordered table-hover">
                    <thead>
                        <tr>
<!--                            <th>
                                <div class="md-checkbox col-md-4" style="margin-left:40%;">
                                    <input type="checkbox" id="check-all-messages" class="md-check"  value="">
                                    <label for="check-all-messages">
                                        <span></span>
                                        <span class="check"></span>
                                        <span class="box"></span>
                                    </label>
                                </div>
                            </th>-->
                            <th>{{_lang('app.pilgrim')}}</th>
                            <th>{{_lang('app.code')}}</th>
                            <th>{{_lang('app.reservation_no')}}</th>
                            <th>{{_lang('app.location')}}</th>
                            <th>{{_lang('app.bus_number')}}</th>
                            <th>{{_lang('app.seat_number')}}</th>
                             <th>{{_lang('app.options')}}</th>


                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pilgrims as $one)
                        <tr>
<!--                            <td>
                                <div class="md-checkbox col-md-4" style="margin-left:40%;">
                                    <input type="checkbox" id="{{$one->id}}" data-id="{{$one->id}}" class="md-check check-one-message"  value="">
                                    <label for="{{$one->id}}">
                                        <span></span>
                                        <span class="check"></span>
                                        <span class="box"></span>
                                    </label>
                                </div>
                            </td>-->
                            <td>{{$one->name}}</td>
                            <td>{{$one->code}}</td>
                            <td>{{$one->reservation_no}}</td>
                            <td>{{$one->location_title}}</td>
                            <td>{{$one->bus_number}}</td>
                            <td>{{$one->seat_number}}</td>
                             <td colspan="2">
                                <a href="" data-id="{{$one->id}}" class="btn btn-circle btn-danger" onclick="BusesAccommodation.delete(this);
                                        return false;">
                                    <i class="fa fa-remove"></i> 
                                </a>
                            </td>

                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
            <div class="text-center">
                {{ $pilgrims->links() }}  
            </div>
            @else
            <p class="text-center">{{_lang('app.no_results')}}</p>
            @endif


        </div>
        <!--row-->
    </div>

</div>

<script>
var new_lang = {

};
var new_config = {
    action: 'index'
};
</script>
@endsection