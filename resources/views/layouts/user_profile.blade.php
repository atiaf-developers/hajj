<!doctype html>
<html>
<head>

	@include('components/front/meta')

</head>

<body>

	@include('components/front/header')

	<div class="container">
	<div id="alert-message" class="alert alert-success fade in" style="display:{{ (session()->has('msg')) ? 'block' : 'none' }};margin-top: 20px;">
        <i class="fa fa-check" aria-hidden="true"></i> 
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <span class="message">
            @if (session()->has('msg'))
            <strong>{{ session('msg') }}</strong>
            @endif
        </span>
         
    </div>

		<h2 class="title">@yield('title')</h2>
		<div class="col-sm-12">
			<div class="col-sm-3">
				<div class="sidenav">
					<ul>
						<li>
							<a href="{{ route('profile') }}"><i class="fa fa-user" aria-hidden="true"></i> {{ _lang('app.profile') }} </a>
						</li>
						<li>
							<a href="{{ route('edit_profile') }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> {{ _lang('app.edit_profile') }}</a>
						</li>
						<li>
							<a href="{{ route('user-favourites') }}"><i class="fa fa-heart-o" aria-hidden="true"></i> {{ _lang('app.favourites') }}</a>
						</li>
						<li>
							<a href="{{ route('user-addresses.index') }}"><i class="fa fa-map-marker" aria-hidden="true"></i> {{ _lang('app.my_addresses') }}</a>
						</li>
					
						 <li class="dropdown-btn">
                         <i class="fa fa-gift" aria-hidden="true"></i> {{ _lang('app.orders') }}
			            </li>
			          
			            <li><a href="{{ route('user-orders.index') }}?type=current">الطلبات الحالية</a></li>
			            <li><a href="{{ route('user-orders.index') }}?type=completed">الطلبات السابقة</a></li>
			           
					</ul>

				</div> 
			</div> 

			<div class="col-sm-9" style="min-height: 700px;">
				@yield('content')
			</div>

		</div>
	</div>


	@include('components/front/footer')

	@include('components/front/scripts')


</body>
</html>
