@extends('layouts.front')

@section('pageTitle',_lang('app.about_us'))

@section('js')

@endsection

@section('content')
<div class="container">
    
        <h2 class="title">العروض</h2>
        @foreach($offers as $offer)
        <div class="bordlin">
            <div class="centers"><a href="{{_url('resturant/'.$offer->resturant_slug)}}" class="imgover"><img src="{{$offer->image}}"> </a></div>
            <div class="divtitle">
                <a href="{{_url('resturant/'.$offer->resturant_slug)}}" class="nam-tit tit-blog">{{$offer->offer}}</a>
                <p class="textblog">{{$offer->resturant_title}} </p>
                <span class="namber">العرض سارى حتى {{$offer->available_until}}</span> </div>
        </div>
        @endforeach
        <!--bordlin-->

        <div class="pager">
            {{ $offers->links() }}  
        </div>

</div>
<!--container-->



@endsection