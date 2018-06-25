@extends('layouts.front')

@section('pageTitle',_lang('app.about_us'))

@section('js')
	
@endsection

@section('content')

  <div class="container minhitcon">
  <div class="row">
    <div class="col-sm-12 lefttbox">
      <h2 class="title-serch">{{ _lang('app.about_us') }}</h2>
      <p class="textcont">
      
           {{ $settings->about_us }}
      </p>
    </div>
    <!--lefttbox--> 
    
  </div>
  <!--row--> 
  
</div>


	
@endsection