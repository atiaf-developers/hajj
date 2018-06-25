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
                        <div class="col-md-8 col-md-offset-2">
               
                            <div class="md-checkbox-inline">
                                @foreach ($buses as $bus) 
                                <div class="md-checkbox has-success">
                                    @php
                                    $_id = $bus->number .  $bus->id 
                                    @endphp
                                    <input type="checkbox" id="{{ $_id }}" name="buses[]" value="{{$bus->id}}" class="md-check">
                                    <label for="{{$_id }}">
                                        <span class="inc"></span>
                                        <span class="check"></span>
                                        <span class="box"></span> {{ $bus->number.' ( '.$bus->available.' )' }} </label>
                                </div>

                                @endforeach

                            </div>
                        </div>

                        <div class="clearfix"></div>



                    </div>
                    <div class="tab">
                        <div class="alert alert-success" style="display:{{Session('successMessage')?'block;':'none;'}}; " role="alert"><i class="fa fa-check" aria-hidden="true"></i> <span class="message">{{Session::get('successMessage')}}</span></div>

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
@endsection