@extends('layouts.backend')

@section('pageTitle',_lang('app.add'))

@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><a href="{{url('admin/buses_accommodation')}}">{{_lang('app.buses_accommodation')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.add')}}</span></li>

@endsection

@section('css')
<link href="{{url('public/backend/css')}}/wizard.css" rel="stylesheet" type="text/css" />
@endsection
@section('js')
<script src="{{url('public/backend/js')}}/buses_accommodation.js" type="text/javascript"></script>
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
                        <h3>{{ _lang('app.enter_buses_accommodation_basic_info') }}</h3>
                        <div class="row">
                            <div class="col-md-8 col-md-offset-2">
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



                        </div>
                    </div>

                    <div class="tab">
                        <div class="alert alert-danger" style="display:{{Session('errorMessage')?'block;':'none;'}}; " role="alert"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <span class="message">{{Session::get('errorMessage')}}</span></div>
                        <h3>{{ _lang('app.select_buses') }}</h3>
                        <div class="pull-right">
                            <p>{{_lang('app.pilgrims')}} ( <span id="pilgrims-count"></span> )</p>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-8 col-md-offset-2" id="buses-box">
               
                        </div>

                        <div class="clearfix"></div>



                    </div>
                    <div class="tab">
                        <div class="alert alert-success" style="display:{{Session('successMessage')?'block;':'none;'}}; " role="alert"><i class="fa fa-check" aria-hidden="true"></i> <span class="message">{{Session::get('successMessage')}}</span></div>
                        <button type="button"  onclick="BusesAccommodation.notify(this);return false;">{{ _lang('app.send_notification') }}</button>
                    </div>
                    <div class="next2">
                        <button type="button" id="nextBtn" data-type="next" onclick="BusesAccommodation.nextPrev(this, 1)">{{ _lang('app.next') }}</button>
                        <button type="button" id="prevBtn" data-type="prev" onclick="BusesAccommodation.nextPrev(this, -1)">{{ _lang('app.back') }}</button>
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
    action: 'add'
};
</script>
@endsection