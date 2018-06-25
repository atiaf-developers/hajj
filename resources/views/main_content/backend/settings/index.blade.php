@extends('layouts.backend')

@section('pageTitle',_lang('app.settings'))

@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.settings')}}</span></li>

@endsection

@section('js')
<script src="{{url('public/backend/js')}}/settings.js" type="text/javascript"></script>
@endsection
@section('content')
<form role="form"  id="editSettingsForm"  enctype="multipart/form-data" >
    {{ csrf_field() }}
    <div class="panel panel-default" id="editSiteSettings">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.basic_info') }}</h3>
        </div>
        <div class="panel-body">


            <div class="form-body">
                

                <div class="form-group form-md-line-input col-md-6">
                    <input type="text" class="form-control" id="email" name="setting[email]" value="{{ isset($settings['email']) ? $settings['email']->value : ''}}">
                    <label for="email">{{_lang('app.email') }}</label>
                    <span class="help-block"></span>
                </div>


                <div class="form-group form-md-line-input col-md-6">
                    <input type="text" class="form-control" id="phone" name="setting[phone]" value="{{isset($settings['phone']) ? $settings['phone']->value : ''}}">
                    <label for="phone">{{_lang('app.phone_1') }}</label>
                    <span class="help-block"></span>
                </div>

                <div class="form-group form-md-line-input col-md-6">
                    <input type="text" class="form-control" id="phone_2" name="setting[phone_2]" value="{{ isset($settings['phone_2']) ? $settings['phone_2']->value : ''}}">
                    <label for="phone_2">{{_lang('app.phone_2') }}</label>
                    <span class="help-block"></span>
                </div>

           

                <div class="clearfix"></div>
            
            </div>




            <!--Table Wrapper Finish-->
        </div>

    </div>







    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.social_media')}}</h3>
        </div>
        <div class="panel-body">

              <div class="form-body">
                <div class="form-group form-md-line-input col-md-6">
                    <input type="text" class="form-control" name="setting[social_media][facebook]" value="{{ isset($settings['social_media']->facebook) ? $settings['social_media']->facebook :'' }}">
                    <label for="social_media_facebook">{{_lang('app.facebook') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-6">
                    <input type="text" class="form-control" name="setting[social_media][twitter]" value="{{ isset($settings['social_media']->twitter) ? $settings['social_media']->twitter :'' }}">
                    <label for="social_media_twitter">{{_lang('app.twitter') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-6">
                    <input type="text" class="form-control" name="setting[social_media][instagram]" value="{{ isset($settings['social_media']->instagram) ? $settings['social_media']->instagram :'' }}">
                    <label for="social_media_instagram">{{_lang('app.instagram') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-6">
                    <input type="text" class="form-control" name="setting[social_media][google]" value="{{ isset($settings['social_media']->google) ?$settings['social_media']->google :'' }}">
                    <label for="social_media_google">{{_lang('app.google') }}</label>
                    <span class="help-block"></span>
                </div>
                <div class="form-group form-md-line-input col-md-6">
                    <input type="text" class="form-control" name="setting[social_media][linkedin]" value="{{ isset($settings['social_media']->linkedin) ? $settings['social_media']->linkedin :'' }}">
                    <label for="social_media_linkedin">{{_lang('app.linkedin') }}</label>
                    <span class="help-block"></span>
                </div>



                <div class="clearfix"></div>
            </div>




            <!--Table Wrapper Finish-->
        </div>
        <div class="panel-footer text-center">
            <button type="button" class="btn btn-info submit-form"
                    >{{_lang('app.save') }}</button>
        </div>

    </div>







</form>
@endsection