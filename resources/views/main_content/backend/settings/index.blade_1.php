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
                    <label for="phone">{{_lang('app.phone') }}</label>
                    <span class="help-block"></span>
                </div>

                <div class="form-group form-md-line-input col-md-6">
                    <input type="text" class="form-control" id="phone_2" name="setting[phone_2]" value="{{ isset($settings['phone_2']) ? $settings['phone_2']->value : ''}}">
                    <label for="phone_2">{{_lang('app.phone#2') }}</label>
                    <span class="help-block"></span>
                </div>

                <div class="form-group form-md-line-input col-md-3 ">
                    <select class="form-control edited" name="setting[about_type]">
                        <option  value="0">{{ _lang('app.text') }}</option>
                        <option  value="1">{{ _lang('app.video') }}</option>
                    </select>
                    <label for="">{{_lang('app.about_us_type') }}</label>
                    <span class="help-block"></span>
                </div>

                <div class="clearfix"></div>
                <div class="form-group form-md-line-input col-md-3">
                    <select class="form-control edited" id="setting[state_of_accommodation]" name="setting[state_of_accommodation]">
                        <option {{ isset($settings['state_of_accommodation']) && $settings['state_of_accommodation']->value == 0 ? 'selected' : '' }} value="0">{{ _lang('app.not_active') }}</option>
                        <option {{ isset($settings['state_of_accommodation']) && $settings['state_of_accommodation']->value == 1 ? 'selected' : '' }} value="1">{{ _lang('app.active') }}</option>

                    </select>
                    <label for="status">{{_lang('app.state_of_accommodation') }}</label>
                    <span class="help-block"></span>
                </div>

                <div class="form-group form-md-line-input col-md-3">
                    <select class="form-control edited" id="setting[upload_profile_image]" name="setting[upload_profile_image]">
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
        <h3 class="panel-title">{{_lang('app.about_us_video') }}</h3>
    </div>

    <div class="panel-body">

        <div class="form-body">
            <div class="col-md-12">


              <div class="form-group form-md-line-input col-md-3 ">
                <select class="form-control edited" name="setting[video_type]" id="video_type">
                    <option value="">{{ _lang('app.choose') }}</option>
                    <option {{ isset($settings['video_type']) && $settings['video_type']->value == 1 ? 'selected' : '' }}  value="1">{{ _lang('app.url') }}</option>
                    <option {{ isset($settings['video_type']) && $settings['video_type']->value == 2 ? 'selected' : '' }} value="2">{{ _lang('app.video') }}</option>
                </select>
                <label for="">{{_lang('app.about_us_video_type') }}</label>
                <span class="help-block"></span>
            </div>

            <div class="clearfix"></div>

            <div class="col-md-6 form-group" id="video_upload" style="{{ $settings['video_type']->value == 2 ? 'display: block': 'display: none' }}">
                <div class="fileinput fileinput-new" data-provides="fileinput">
                    <span class="btn green btn-file">
                        <span class="fileinput-new"> Select file </span>
                        <span class="fileinput-exists"> Change </span>
                        <input type="hidden" value="" name="..."><input type="file" name="setting[about_video_url]"> </span>
                        <span class="fileinput-filename"></span> &nbsp;
                        <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"> </a>
                    </div>
                    <span class="help-block">

                    </span>
                    <video width="320" height="240" controls>
                       <source src="{{ url('public/uploads/videos').'/'.$settings['about_video_url']->value }}" type="video/mp4">
                       </video>
                   </div>


                   <div id="video_url" style="{{ $settings['video_type']->value == 1 ? 'display: block': 'display: none' }}">


                    <div class="form-group form-md-line-input col-md-6">

                        <input type="hidden" name="setting[youtube_url]" id="youtube_url" value="{{ isset($settings['youtube_url']) ? $settings['youtube_url']->value : '' }}">

                        <input type="text" class="form-control" id="url" name="setting[about_video_url]" value="{{ isset($settings['about_video_url']) && $settings['video_type']->value == 1 ? $settings['about_video_url']->value :'' }}">

                        <label for="">{{_lang('app.about_video_url') }}</label>

                        <span class="help-block"></span>
                    </div>

                    <div id="youtube-iframe" class="col-md-12">
                     <iframe width="100%" height="315" src="//www.youtube.com/embed/{{$settings['youtube_url']->value}}" frameborder="0" allowfullscreen></iframe>
                 </div>



             </div>



         </div>
     </div>




     <!--Table Wrapper Finish-->
 </div>

</div>

<div class="panel panel-default">
 <div class="panel-heading">
    <h3 class="panel-title">{{_lang('app.declrative_video') }}</h3>
</div>

<div class="panel-body">

    <div class="form-body">
        <div class="col-md-12">


          <div class="form-group form-md-line-input col-md-3 ">
            <select class="form-control edited" name="setting[declarative_video_type]" id="declarative_video_type">
                <option value="">{{ _lang('app.choose') }}</option>
                <option {{ isset($settings['declarative_video_type']) && $settings['declarative_video_type']->value == 1 ? 'selected' : '' }}  value="1">{{ _lang('app.url') }}</option>
                <option {{ isset($settings['declarative_video_type']) && $settings['declarative_video_type']->value == 2 ? 'selected' : '' }} value="2">{{ _lang('app.video') }}</option>
            </select>
            <label for="">{{_lang('app.declrative_video_type') }}</label>
            <span class="help-block"></span>
        </div>

        <div class="clearfix"></div>

        <div class="col-md-6 form-group" id="declrative_video_upload" style="{{ isset($settings['declarative_video_type']) && $settings['declarative_video_type']->value == 2 ? 'display: block': 'display: none' }}">
            <div class="fileinput fileinput-new" data-provides="fileinput">
                <span class="btn green btn-file">
                    <span class="fileinput-new"> Select file </span>
                    <span class="fileinput-exists"> Change </span>
                    <input type="hidden" value="" name="..."><input type="file" name="setting[declarative_video_url]"> </span>
                    <span class="fileinput-filename"></span> &nbsp;
                    <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"> </a>
                </div>
                <span class="help-block">

                </span>
                <video width="320" height="240" controls>
                   <source src="{{ url('public/uploads/videos').'/'.$settings['declarative_video_url']->value }}" type="video/mp4">
                   </video>
               </div>


               <div id="declrative_video_url" style="{{ isset($settings['declarative_video_type']) && $settings['declarative_video_type']->value == 1 ? 'display: block': 'display: none' }}">


                <div class="form-group form-md-line-input col-md-6">

                    <input type="hidden" name="setting[declarative_video_youtube_url]" id="declarative_youtube_url" value="{{ isset($settings['declarative_youtube_url']) ? $settings['declarative_video_youtube_url']->value : '' }}">

                    <input type="text" class="form-control" id="declrative-url" name="setting[declarative_video_url]" value="{{ isset($settings['declarative_video_url']) && $settings['declarative_video_type']->value == 1 ? $settings['declarative_video_url']->value :'' }}">

                    <label for="">{{_lang('app.declarative_video_url') }}</label>

                    <span class="help-block"></span>
                </div>

                <div id="declrative-youtube-iframe" class="col-md-12">
                 <iframe width="100%" height="315" src="//www.youtube.com/embed/{{ isset($settings['declarative_video_youtube_url']) ?  $settings['declarative_video_youtube_url']->value : "" }}" frameborder="0" allowfullscreen></iframe>
             </div>



         </div>



     </div>
 </div>




 <!--Table Wrapper Finish-->
</div>

</div>

    {{--  <div class="panel panel-default">

        <div class="panel-body">

            <div class="form-body">  --}}

                @foreach ($languages as $key => $value)
                <div class="panel panel-default">

                    <div class="panel-body">

                        <div class="form-body">
                            <div class="col-md-12">


                                <div class="form-group form-md-line-input col-md-3">
                                    <textarea class="form-control" id="about_text[{{ $key }}]" name="about_text[{{ $key }}]"  cols="30" rows="10">{{isset($settings_translations[$key])?$settings_translations[$key]->about_text:''}}</textarea>
                                    <label for="about_text">{{_lang('app.about_text') }} {{ _lang('app. '.$value.'') }}</label>
                                    <span class="help-block"></span>
                                </div>


                            </div>
                        </div>




                        <!--Table Wrapper Finish-->
                    </div>

                </div>
                @endforeach


                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{_lang('app.supervisors_info') }}</h3>
                    </div>
                    <div class="panel-body">

                        <div class="form-body">
                           <div class="row">
                              <h3>{{ _lang('app.mena_site_supervisor') }}</h3>
                              <div class="form-group col-md-3">
                                <label class="control-label">{{_lang('app.image')}}</label>

                                <div class="mena_supervisor_image_box">
                                    <img src="{{ isset($settings['mena_supervisor']->image) ? url('public/uploads/supervisors').'/'.$settings['mena_supervisor']->image :url('no-image.png') }}" width="100" height="80" class="mena_supervisor_image" />
                                </div>
                                <input type="file" name="setting[mena_supervisor][image]" id="mena_supervisor_image" style="display:none;">     
                                <span class="help-block"></span>             
                            </div>



                            <div class="form-group form-md-line-input col-md-3">
                                <input type="text" class="form-control" id="setting[mena_supervisor][name]" name="setting[mena_supervisor][name]" value="{{ isset($settings['mena_supervisor']->name) ? $settings['mena_supervisor']->name :'' }}">
                                <label for="name">{{_lang('app.supervisor_name') }}</label>
                                <span class="help-block"></span>
                            </div>


                            <div class="form-group form-md-line-input col-md-6">
                                <input type="text" class="form-control" id="setting[mena_supervisor][contact_numbers]" name="setting[mena_supervisor][contact_numbers]" value="{{ isset($settings['mena_supervisor']->contact_numbers) ? $settings['mena_supervisor']->contact_numbers :'' }}" placeholder="+966663635,+96651515156,....">
                                <label for="supervisor_contact_numbers">{{_lang('app.supervisor_contact_numbers') }}</label>
                                <span class="help-block"></span>
                            </div>

                        </div>


                        <div class="row">
                          <h3>{{ _lang('app.muzdalifah_site_supervisor') }}</h3>
                          <div class="form-group col-md-3">
                            <label class="control-label">{{_lang('app.image')}}</label>

                            <div class="muzdalifah_supervisor_image_box">
                                <img src="{{ isset($settings['muzdalifah_supervisor']->image) ? url('public/uploads/supervisors').'/'.$settings['muzdalifah_supervisor']->image :url('no-image.png') }}" width="100" height="80" class="muzdalifah_supervisor_image" />
                            </div>
                            <input type="file" name="setting[muzdalifah_supervisor][image]" id="muzdalifah_supervisor_image" style="display:none;">     
                            <span class="help-block"></span>             
                        </div>



                        <div class="form-group form-md-line-input col-md-3">
                            <input type="text" class="form-control" id="setting[muzdalifah_supervisor][name]" name="setting[muzdalifah_supervisor][name]" value="{{ isset($settings['muzdalifah_supervisor']->name) ? $settings['muzdalifah_supervisor']->name :'' }}">
                            <label for="name">{{_lang('app.supervisor_name') }}</label>
                            <span class="help-block"></span>
                        </div>


                        <div class="form-group form-md-line-input col-md-6">
                            <input type="text" class="form-control" id="setting[muzdalifah_supervisor][contact_numbers]" name="setting[muzdalifah_supervisor][contact_numbers]" value="{{ isset($settings['muzdalifah_supervisor']->contact_numbers) ? $settings['muzdalifah_supervisor']->contact_numbers :'' }}" placeholder="+966663635,+96651515156,....">
                            <label for="supervisor_contact_numbers">{{_lang('app.supervisor_contact_numbers') }}</label>
                            <span class="help-block"></span>
                        </div>

                    </div>


                    <div class="row">
                      <h3>{{ _lang('app.arafat_site_supervisor') }}</h3>
                      <div class="form-group col-md-3">
                        <label class="control-label">{{_lang('app.image')}}</label>

                        <div class="arafat_supervisor_image_box">
                            <img src="{{ isset($settings['arafat_supervisor']->image) ? url('public/uploads/supervisors').'/'.$settings['arafat_supervisor']->image : url('no-image.png') }}" width="100" height="80" class="arafat_supervisor_image" />
                        </div>
                        <input type="file" name="setting[arafat_supervisor][image]" id="arafat_supervisor_image" style="display:none;">     
                        <span class="help-block"></span>             
                    </div>



                    <div class="form-group form-md-line-input col-md-3">
                        <input type="text" class="form-control" id="setting[arafat_supervisor][name]" name="setting[arafat_supervisor][name]" value="{{ isset($settings['arafat_supervisor']->name) ? $settings['arafat_supervisor']->name :'' }}">
                        <label for="name">{{_lang('app.supervisor_name') }}</label>
                        <span class="help-block"></span>
                    </div>


                    <div class="form-group form-md-line-input col-md-6">
                        <input type="text" class="form-control" id="setting[arafat_supervisor][contact_numbers]" name="setting[arafat_supervisor][contact_numbers]" value="{{ isset($settings['muzdalifah_supervisor']->contact_numbers) ? $settings['muzdalifah_supervisor']->contact_numbers :'' }}" placeholder="+966663635,+96651515156,....">
                        <label for="supervisor_contact_numbers">{{_lang('app.supervisor_contact_numbers') }}</label>
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