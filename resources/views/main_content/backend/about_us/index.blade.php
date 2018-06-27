

@extends('layouts.backend')

@section('pageTitle',_lang('app.about_us'))

@section('breadcrumb')
<li><a href="{{url('admin')}}">{{_lang('app.dashboard')}}</a> <i class="fa fa-circle"></i></li>
<li><span> {{_lang('app.about_us')}}</span></li>

@endsection

@section('js')
<script src="{{url('public/backend/js')}}/about_us.js" type="text/javascript"></script>
@endsection
@section('content')
<form role="form"  id="editAboutUsForm"  enctype="multipart/form-data" >
    {{ csrf_field() }}

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.about') }}</h3>
        </div>
        <div class="panel-body">


            <div class="form-body">
                <div class="col-md-12" style="margin-top: 20px;">
                    <div class="md-radio col-md-3">
                        <input type="radio" id="about_text_radio" {{$settings['about_type']->value==0?'checked':''}} name="setting[about_type]" value="0" class="md-radiobtn">
                        <label for="about_text_radio">
                            <span></span>
                            <span class="check"></span>
                            <span class="box"></span>
                            {{_lang('app.text')}} </label>
                    </div>

                    <div class="col-md-9">
                        @foreach ($languages as $key => $value)
                        <div class="form-group form-md-line-input">
                            <textarea class="form-control" id="about_text[{{ $key }}]" name="setting[info][about_text][{{ $key }}]"  cols="30" rows="10">{{isset($settings['info'])&&isset($settings['info']->about_text->$key)?$settings['info']->about_text->$key:''}}</textarea>
                            <label for="about_text">{{ _lang('app. '.$value) }}</label>
                            <span class="help-block"></span>
                        </div>
                        @endforeach
                    </div>
                </div>

<!--                <div class="col-md-12" style="margin-top: 20px;">
                    <div class="md-radio col-md-3">
                        <input type="radio" id="about_video_url_radio" {{$settings['about_type']->value==1?'checked':''}} name="setting[about_type]" value="1" class="md-radiobtn">
                        <label for="about_video_url_radio">
                            <span></span>
                            <span class="check"></span>
                            <span class="box"></span>
                            {{_lang('app.video')}} </label>
                    </div>
                    <div class="col-md-9">
                        <div class="form-group">
                            <div class="fileinput fileinput-new pull-right" data-provides="fileinput">
                                <span class="btn green btn-file">
                                    <span class="fileinput-new"> Select file </span>
                                    <span class="fileinput-exists"> Change </span>
                                    <input type="hidden" value="" name="..."><input type="file" name="about_video_url"> </span>
                                <span class="fileinput-filename"></span> &nbsp;
                                <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"> </a>
                            </div>
                            <span class="help-block"></span>
                        </div>

                        <video width="100%" height="315" controls>
                            <source src="{{ url('public/uploads/videos').'/'.$settings['about_video_url']->value }}" type="video/mp4">
                        </video>
                    </div>

                </div>-->

                <div class="col-md-12" style="margin-top: 20px;">
                    <div class="md-radio col-md-3">
                        <input type="radio" id="about_youtube_url_radio" {{$settings['about_type']->value==2?'checked':''}} name="setting[about_type]" value="2" class="md-radiobtn">
                        <label for="about_youtube_url_radio">
                            <span></span>
                            <span class="check"></span>
                            <span class="box"></span>
                            {{_lang('app.youtube')}} </label>
                    </div>
                    <div class="col-md-9">
                        <div class="form-group col-md-9">
                            <input type="hidden" id="about_youtube_url" name="setting[about_youtube_url]" value="{{ isset($settings['about_youtube_url']) ? $settings['about_youtube_url']->value : '' }}">
                            <input type="text" class="form-control" id="about_youtube_url_input" name="about_youtube_url_input" value="">
                            <span class="help-block"></span>
                        </div>

                        <div id="about-youtube-iframe" class="col-md-12">
                            <iframe width="100%" height="315" src="//www.youtube.com/embed/{{$settings['about_youtube_url']->value}}" frameborder="0" allowfullscreen></iframe>
                        </div>
                    </div>

                </div>
                <div class="clearfix"></div>



            </div>




            <!--Table Wrapper Finish-->
        </div>

    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{_lang('app.declarative') }}</h3>
        </div>
        <div class="panel-body">


            <div class="form-body">
           

<!--                <div class="col-md-12" style="margin-top: 20px;">
                    <div class="md-radio col-md-3">
                        <input type="radio" id="declarative_video_url_radio" {{$settings['declarative_type']->value==0?'checked':''}} name="setting[declarative_type]" value="0" class="md-radiobtn">
                        <label for="declarative_video_url_radio">
                            <span></span>
                            <span class="check"></span>
                            <span class="box"></span>
                            {{_lang('app.video')}} </label>
                    </div>
                    <div class="col-md-9">
                        <div class="form-group">
                            <div class="fileinput fileinput-new pull-right" data-provides="fileinput">
                                <span class="btn green btn-file">
                                    <span class="fileinput-new"> Select file </span>
                                    <span class="fileinput-exists"> Change </span>
                                    <input type="hidden" value="" name="..."><input type="file" name="declarative_video_url"> </span>
                                <span class="fileinput-filename"></span> &nbsp;
                                <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"> </a>
                            </div>
                            <span class="help-block"></span>
                        </div>

                        <video width="100%" height="315" controls>
                            <source src="{{ url('public/uploads/videos').'/'.$settings['declarative_video_url']->value }}" type="video/mp4">
                        </video>
                    </div>

                </div>-->

                <div class="col-md-12" style="margin-top: 20px;">
                    <div class="md-radio col-md-3">
                        <input type="radio" id="declarative_youtube_url_radio" {{$settings['declarative_type']->value==1?'checked':''}} name="setting[declarative_type]" value="1" class="md-radiobtn">
                        <label for="declarative_youtube_url_radio">
                            <span></span>
                            <span class="check"></span>
                            <span class="box"></span>
                            {{_lang('app.youtube')}} </label>
                    </div>
                    <div class="col-md-9">
                        <div class="form-group col-md-9">
                            <input type="hidden" id="declarative_youtube_url" name="setting[declarative_youtube_url]" value="{{ isset($settings['declarative_youtube_url']) ? $settings['declarative_youtube_url']->value : '' }}">
                            <input type="text" class="form-control" id="declarative_youtube_url_input" name="setting[declarative_youtube_url_input]" value="">
                            <span class="help-block"></span>
                        </div>

                        <div id="declarative-youtube-iframe" class="col-md-12">
                            <iframe width="100%" height="315" src="//www.youtube.com/embed/{{$settings['declarative_youtube_url']->value}}" frameborder="0" allowfullscreen></iframe>
                        </div>
                    </div>

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