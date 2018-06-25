@extends('layouts.front')

@section('pageTitle',_lang('app.usage_conditions') )

@section('js')
	
@endsection

@section('content')

  <div class="container minhitcon">
  <div class="row">
    <div class="col-sm-12 lefttbox">
      <h2 class="title-serch">{{ _lang('app.usage_conditions') }}</h2>
      <p class="textcont">
      
           {{ $settings->usage_conditions }}
      </p>
    </div>
    <!--lefttbox--> 
    
  </div>
  <!--row--> 
  
</div>


	
@endsection