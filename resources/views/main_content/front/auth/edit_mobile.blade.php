@extends('layouts.front')

@section('pageTitle','Ga3aaan - Edit-Mobile')

@section('js')
  <script src=" {{ url('public/front/scripts') }}/login.js"></script>
@endsection

@section('content')

<div class="container">
  <div class="centerbolog">
    <h1 class="title">{{ _lang('app.edit_mobile_number') }}</h1>
   

    <form class="text-in" method="post" action="{{ route('editphone') }}" id = "editMobileForm">
     {{ csrf_field() }}
    <div id="alert-message" class="alert alert-success" style="display:{{ ($errors->has('msg')) ? 'block' : 'none' }};margin-top: 20px;">
        <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> 
        <span class="message">
            @if ($errors->has('msg'))
            <strong>{{ $errors->first('msg') }}</strong>
            @endif
        </span>
    </div>
      
      <div class="row form-group">

         
        <div class="col-sm-12 inputbox form-group">

          <input type="text" name="mobile" class="form-control " placeholder="0">
           <span class="help-block">
               @if ($errors->has('mobile'))
                 {{ $errors->first('mobile') }}
               @endif
           </span>
        </div>
       
       
        <div class="col-sm-12 inputbox mapx">
          <button type="submit" class="botoom submit-form">{{ _lang('app.Edit_mobile') }}</button>
        </div>

        
      </div>
      <!--row--> 
    </form>
  </div>
  <!--centerbolog--> 
  
</div>




  
@endsection