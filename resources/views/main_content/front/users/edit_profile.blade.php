@extends('layouts.user_profile')
@section('title')
  {{ _lang('app.edit_profile') }}
@endsection
@section('js')
	<script src=" {{ url('public/front/scripts') }}/users.js"></script>
@endsection


@section('content')



<form action="{{ route('update_user') }}" method="post" class="form-uecl" id="editProfile">


	{{ csrf_field() }}
	<div class="row">

		<div class="col-sm-12 input">
			<div class="row form-group">
				<label class="col-sm-4 control-label"> {{ _lang('app.first_name') }} </label>
				<div class="col-sm-8">
					<input type="text" name="first_name" class="form-control" placeholder="" value="{{ $User->first_name }}" id="first_name">
				</div>
				 <span class="help-block">
		             @if ($errors->has('first_name'))
		                {{ $errors->first('first_name') }}
		              @endif
		         </span>
			</div>
		</div>
		<!--input-->

		<div class="col-sm-12 input">
			<div class="row form-group">
				<label class="col-sm-4 control-label">{{ _lang('app.last_name') }} </label>
				<div class="col-sm-8">
					<input type="text" name="last_name" class="form-control" placeholder="" value="{{ $User->last_name }}" id="last_name">
				</div>
				<span class="help-block">
		             @if ($errors->has('last_name'))
		                {{ $errors->first('last_name') }}
		              @endif
		         </span>
			</div>
		</div>
		<!--input-->

		<div class="col-sm-12 input">
			<div class="row form-group">
				<label class="col-sm-4 control-label"> {{ _lang('app.email') }} </label>
				<div class="col-sm-8">
					<input type="email" name="email" class="form-control" placeholder="" value="{{ $User->email }}" id="email">
				</div>
				<span class="help-block">
		             @if ($errors->has('email'))
		                {{ $errors->first('email') }}
		              @endif
		         </span>
			</div>
		</div>
		<!--input-->

		<div class="col-sm-12 input">
			<div class="row form-group">
				<label class="col-sm-4 control-label"> {{ _lang('app.mobile') }} </label>
				<div class="col-sm-8">
					<input type="number" name="mobile" class="form-control" placeholder="" value="{{ $User->mobile }}" id="mobile">
				</div>
				<span class="help-block">
		             @if ($errors->has('mobile'))
		                {{ $errors->first('mobile') }}
		              @endif
		        </span>
			</div>
		</div>
		<!--input--> 

		<div class="col-sm-12 input">
			<div class="row form-group">
				<label class="col-sm-4 control-label">{{ _lang('app.change_image') }}</label>
				<div class="col-sm-8">
					@php
					$image = $User->user_image ? $User->user_image : 'default.png';
					@endphp
					<div class="imguser"> <img class="img_prev" src="{{ url('public/uploads/users/'.$image)}}" width="100" height="100"></div>
					<label class="btn btn-info"><i class="fa fa-camera" aria-hidden="true"></i> {{ _lang('app.upload') }}
						<input type="file" name="user_image"  onchange="readURL(this);" style="display: none;" id="user_image">
					</label>
				</div>
				<span class="help-block">
		             @if ($errors->has('user_image'))
		                {{ $errors->first('user_image') }}
		              @endif
		         </span>
			</div>
		</div>


		<div class="col-sm-12 input">
			<div class="row form-group">
				<label class="col-sm-4 control-label">{{ _lang('app.password') }}</label>
				<div class="col-sm-8">
					<input type="password" name="password" class="form-control" placeholder="" id="password">
				</div>
				<span class="help-block">
		             @if ($errors->has('password'))
		                {{ $errors->first('password') }}
		              @endif
		         </span>
			</div>
		</div>
		<!--input-->

		<div class="col-sm-12 input">
			<div class="row form-group">
				<label class="col-sm-4 control-label">{{ _lang('app.confirm_password') }}</label>
				<div class="col-sm-8">
					<input type="password" name="confirm_password" class="form-control" placeholder="" id="confirm_password">
				</div>
				<span class="help-block">
		             @if ($errors->has('confirm_password'))
		                {{ $errors->first('confirm_password') }}
		              @endif
		         </span>
			</div>
		</div>
		<!--input-->

		<div class="col-sm-12 input col-sm-offset-4">
			<button class="botoom witouto submit-form" type="submit"> {{ _lang('app.edit') }}</button>
		</div>
	</div>
</form>
@endsection