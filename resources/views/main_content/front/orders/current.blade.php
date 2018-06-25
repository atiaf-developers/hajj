@extends('layouts.user_profile')
@section('title')
  {{ _lang('app.current_orders') }}
@endsection
@section('js')

@endsection

@section('content')
	

      @foreach ($orders as $order)
         
      <div class="bordlin">
        <div class="centers">
          <a href="#" class="imgover">
           <img src="{{ $order->resturant_image }}"> 
        </a>
      </div>
        <div class="divtitle">
         <a href="{{route('user-orders.show',Crypt::encrypt($order->id))}}"><h3 class="nam-tit">{{ $order->resturant }} - {{ $order->region }}</h3></a>
          <span class="namber">{{ $order->status_text }}</span> </div>
      </div>
	
      @endforeach



<div class="pager">
    {{ $orders->appends($_GET)->links() }}  
</div>
		
@endsection