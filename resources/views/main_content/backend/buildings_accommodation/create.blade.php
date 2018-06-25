@extends('layouts.backend')

@section('pageTitle',_lang('app.create'))

@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{url('admin/buildings_accommodation')}}">{{_lang('app.buildings_accommodation')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.create')}}</span></li>

@endsection
@section('css')
<link href="{{url('public/backend/css')}}/wizard.css" rel="stylesheet" type="text/css" />
@endsection
@section('js')
<script src="{{url('public/backend/js')}}/buildings_accommodation.js" type="text/javascript"></script>
@endsection
@section('content')

<section id="login">

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="login-area">
                <form id="regForm">
                    {{ csrf_field() }}
                    <!--<img class="user" src="{{ url('public/front/img') }}/signin1.png" alt="" >-->
                    <!-- One "tab" for each step in the form: -->
                    <div class="tab">
                        <div class="alert alert-danger" style="display:{{Session('errorMessage')?'block;':'none;'}}; " role="alert"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <span class="message">{{Session::get('errorMessage')}}</span></div>
                        <h3>{{ _lang('app.enter_accommodation_basic_info') }}</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group form-md-line-input">
                                    <select class="form-control" id="location" name="location">
                                        <option  value="">{{ _lang('app.choose') }}</option>
                                        @foreach ($locations as $one)
                                        <option value = "{{$one->id}}">{{$one->title}}</option>
                                        @endforeach
                                    </select>
                                    <label for = "location">{{_lang('app.locations')}}</label>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group form-md-line-input">
                                    <select class="form-control" id="pilgrim_class" name="pilgrim_class">
                                        <option  value="">{{ _lang('app.choose') }}</option>
                                        @foreach ($pilgrims_class as $one)
                                        <option value = "{{$one->id}}">{{$one->title}}</option>
                                        @endforeach
                                    </select>
                                    <label for = "pilgrim_class">{{_lang('app.pilgrims_class')}}</label>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group form-md-line-input">
                                    <select class="form-control" id="gender" name="gender">
                                        <option  value="">{{ _lang('app.choose') }}</option>
                                        <option  value="1">{{ _lang('app.male') }}</option>
                                        <option  value="2">{{ _lang('app.female') }}</option>
                                    </select>
                                    <label for="">{{_lang('app.gender') }}</label>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group form-md-line-input">
                                    <select class="form-control" name="type">
                                        <option  value="">{{ _lang('app.choose') }}</option>
                                        @foreach($types as $key=> $type)
                                        @if(in_array($key,[0,1,2,3]))
                                        <option  value="{{$key}}">{{ str_replace(' ','-',_lang('app.'.$type)) }}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                    <label for="">{{_lang('app.way_of_accommodation') }}</label>
                                    <span class="help-block"></span>
                                </div>
                            </div>


                        </div>
                    </div>

                    <div class="tab">
                        <div class="alert alert-danger" style="display:{{Session('errorMessage')?'block;':'none;'}}; " role="alert"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <span class="message">{{Session::get('errorMessage')}}</span></div>
                        <h3>{{ _lang('app.select_rooms') }}</h3>
                        <div class="pull-left">
                                <a class="btn btn-primary add-suite" style="margin-bottom: 10px;">{{_lang('app.add_new_suite')}}</a>
                        </div>
                        <div class="pull-right">
                            <p>{{_lang('app.pilgrims')}} ( <span id="pilgrims-count"></span> )</p>
                        </div>
                        <div class="clearfix"></div>
                        <!--<a class="btn btn-primary add-lounge">{{_lang('app.add')}}</a>-->

                        <table class="table" id="rooms-table">
                            <tbody>            

                                <tr class="room-one">
                                    <td style="width:40%;">
                                        <div class="form-group form-md-line-input col-md-6">
                                            <select class="form-control" name="building">
                                                <option  value="">{{ _lang('app.choose') }}</option>
                                                @foreach($buildings as $one)
                                                <option  value="{{ $one->id }}">{{ $one->number }}</option>
                                                @endforeach
                                            </select>
                                            <label for = "building">{{_lang('app.building')}}</label>
                                            <span class="help-block"></span>
                                        </div>
                                        <div class="form-group form-md-line-input col-md-6">
                                            <select class="form-control" id="floor1" name="floor">
                                                <option  value="">{{ _lang('app.choose') }}</option>

                                            </select>
                                            <label for = "floor">{{_lang('app.floor')}}</label>
                                            <span class="help-block"></span>
                                        </div>
                                    </td>
                                    <td class="rooms" style="width:55%;">
                                        <label>{{_lang('app.rooms')}}</label>
                                        <div class="box">

                                        </div>
                                    </td>
                                    <td style="width:5%;">
                                        <a class="btn btn-danger remove-suite">{{_lang('app.remove')}}</a>
                                    </td>
                                </tr>


                                </tr>
                            </tbody>
                        </table>




                    </div>
                    <div class="tab">
                        <div class="alert alert-success" style="display:{{Session('successMessage')?'block;':'none;'}}; " role="alert"><i class="fa fa-check" aria-hidden="true"></i> <span class="message">{{Session::get('successMessage')}}</span></div>
                          <button type="button"  onclick="BuildingsAccommodation.notify(this);return false;">{{ _lang('app.send_notification') }}</button>
                    </div>
                    <div class="next2">
                        <button type="button" id="nextBtn" data-type="next" onclick="BuildingsAccommodation.nextPrev(this, 1)">{{ _lang('app.next') }}</button>
                        <button type="button" id="prevBtn" data-type="prev" onclick="BuildingsAccommodation.nextPrev(this, -1)">{{ _lang('app.back') }}</button>
                    </div>
                    <!-- Circles which indicates the steps of the form: -->
                    <div class="steps">
                        <span class="step"></span>
                        <span class="step"></span>
                        <span class="step"></span>
                    </div>
                </form>

            </div>
        </div>
    </div>

</section>
<script>
var new_lang = {

};
var new_config = {
    action: 'add',
    buildings: '{!!json_encode($buildings)!!}'
};
</script>
@endsection