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
                <div class="form-group form-md-line-input col-md-3">
                    <select class="form-control" id="setting[state_of_accommodation]" name="setting[state_of_accommodation]">
                        <option {{ isset($settings['state_of_accommodation']) && $settings['state_of_accommodation']->value == 0 ? 'selected' : '' }} value="0">{{ _lang('app.not_active') }}</option>
                        <option {{ isset($settings['state_of_accommodation']) && $settings['state_of_accommodation']->value == 1 ? 'selected' : '' }} value="1">{{ _lang('app.active') }}</option>

                    </select>
                    <label for="status">{{_lang('app.state_of_accommodation') }}</label>
                    <span class="help-block"></span>
                </div>

                <div class="form-group form-md-line-input col-md-3">
                    <select class="form-control" id="setting[upload_profile_image]" name="setting[upload_profile_image]">
                        <option {{ isset($settings['upload_profile_image']) && $settings['upload_profile_image']->value == 0 ? 'selected' : '' }} value="0">{{ _lang('app.not_active') }}</option>
                        <option {{ isset($settings['upload_profile_image']) && $settings['upload_profile_image']->value == 1 ? 'selected' : '' }} value="1">{{ _lang('app.active') }}</option>

                    </select>
                    <label for="status">{{_lang('app.upload_profile_image') }}</label>
                    <span class="help-block"></span>
                </div>



                <div class="clearfix"></div>

            </div>




            <!--Table Wrapper Finish-->
        </div>

    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.supervisors_info') }}</h3>
        </div>
        <div class="panel-body">

            <div class="form-body">
                <div class="col-md-12">
                    <h3>{{ _lang('app.mena_site_supervisor') }}</h3>
                
                    <div class="form-group form-md-line-input col-md-3">
                        <input type="text" class="form-control" id="setting[mena_supervisor][name]" name="supervisors[0][name]" value="{{ isset($settings['supervisors'][0]->name) ? $settings['supervisors'][0]->name :'' }}">
                        <label for="name">{{_lang('app.supervisor_name') }}</label>
                        <span class="help-block"></span>
                    </div>


                    <div class="form-group form-md-line-input col-md-7">
                        <input type="text" class="form-control" id="setting[mena_supervisor][contact_numbers]" name="supervisors[0][contact_numbers]" value="{{ isset($settings['supervisors'][0]->contact_numbers) ? $settings['supervisors'][0]->contact_numbers :'' }}" placeholder="+966663635,+96651515156,....">
                        <label for="supervisor_contact_numbers">{{_lang('app.supervisor_contact_numbers') }}</label>
                        <span class="help-block"></span>
                    </div>
                    <div class="form-group col-md-2">
                        <label class="control-label">{{_lang('app.image')}}</label>

                        <div class="mena_supervisor_image_box">
                            <img src="{{ isset($settings['supervisors'][0]) ? url('public/uploads/supervisors').'/'.$settings['supervisors'][0]->image :url('no-image.png') }}" width="100" height="80" class="mena_supervisor_image" />
                        </div>
                        <input type="file" name="supervisors[0][image]" id="mena_supervisor_image" style="display:none;">     
                        <span class="help-block"></span>             
                    </div>

                </div>
                <div class="col-md-12">
                    <h3>{{ _lang('app.muzdalifah_site_supervisor') }}</h3>
                
                    <div class="form-group form-md-line-input col-md-3">
                        <input type="text" class="form-control" id="setting[mena_supervisor][name]" name="supervisors[1][name]" value="{{ isset($settings['supervisors'][1]->name) ? $settings['supervisors'][1]->name :'' }}">
                        <label for="name">{{_lang('app.supervisor_name') }}</label>
                        <span class="help-block"></span>
                    </div>


                    <div class="form-group form-md-line-input col-md-7">
                        <input type="text" class="form-control" id="setting[mena_supervisor][contact_numbers]" name="supervisors[1][contact_numbers]" value="{{ isset($settings['supervisors'][1]->contact_numbers) ? $settings['supervisors'][1]->contact_numbers :'' }}" placeholder="+966663635,+96651515156,....">
                        <label for="supervisor_contact_numbers">{{_lang('app.supervisor_contact_numbers') }}</label>
                        <span class="help-block"></span>
                    </div>
                    <div class="form-group col-md-2">
                        <label class="control-label">{{_lang('app.image')}}</label>

                        <div class="muzdalifah_supervisor_image_box">
                            <img src="{{ isset($settings['supervisors'][1]) ? url('public/uploads/supervisors').'/'.$settings['supervisors'][1]->image :url('no-image.png') }}" width="100" height="80" class="muzdalifah_supervisor_image" />
                        </div>
                        <input type="file" name="supervisors[1][image]" id="muzdalifah_supervisor_image" style="display:none;">     
                        <span class="help-block"></span>             
                    </div>

                </div>
                <div class="col-md-12">
                    <h3>{{ _lang('app.arafat_site_supervisor') }}</h3>
                
                    <div class="form-group form-md-line-input col-md-3">
                        <input type="text" class="form-control" id="setting[mena_supervisor][name]" name="supervisors[2][name]" value="{{ isset($settings['supervisors'][2]->name) ? $settings['supervisors'][2]->name :'' }}">
                        <label for="name">{{_lang('app.supervisor_name') }}</label>
                        <span class="help-block"></span>
                    </div>


                    <div class="form-group form-md-line-input col-md-7">
                        <input type="text" class="form-control" id="setting[mena_supervisor][contact_numbers]" name="supervisors[2][contact_numbers]" value="{{ isset($settings['supervisors'][2]->contact_numbers) ? $settings['supervisors'][2]->contact_numbers :'' }}" placeholder="+966663635,+96651515156,....">
                        <label for="supervisor_contact_numbers">{{_lang('app.supervisor_contact_numbers') }}</label>
                        <span class="help-block"></span>
                    </div>
                    <div class="form-group col-md-2">
                        <label class="control-label">{{_lang('app.image')}}</label>

                        <div class="arafat_supervisor_image_box">
                            <img src="{{ isset($settings['supervisors'][1]) ? url('public/uploads/supervisors').'/'.$settings['supervisors'][2]->image :url('no-image.png') }}" width="100" height="80" class="arafat_supervisor_image" />
                        </div>
                        <input type="file" name="supervisors[2][image]" id="arafat_supervisor_image" style="display:none;">     
                        <span class="help-block"></span>             
                    </div>

                </div>
                


              


            </div>
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