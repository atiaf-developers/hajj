@extends('layouts.front')

@section('pageTitle','Ga3aaan')

@section('js')
<script src=" {{ url('public/front/scripts') }}/main.js"></script>
@endsection



@section('content')

<div class="container">
    <h2 class="title"> المنتجات</h2>
    <div class="row">
        @foreach($meals as $meal)
        <div class="col-sm-3 photbx">
            <div class="in-boxs">
            
                
                <a href="{{_url('resturant/'.$meal->resturant_slug.'/'.$meal->menu_section_slug.'/'.$meal->slug)}}" class="property-box">
                </a>

                <a href="{{ route('add-favourite',$meal->slug) }}" class="property-box {{$meal->is_favourite?'active':''}}"  data-slug="{{ $meal->slug }}" onclick="event.preventDefault(); main.handleFavourites(this)">
                    <i class="fa fa-heart-o" aria-hidden="true"></i>
                </a>

                <img src="{{$meal->image}}"> 
                <a href="{{_url('resturant/'.$meal->resturant_slug.'/'.$meal->menu_section_slug.'/'.$meal->slug)}}" class="nam-tit">{{$meal->title}}</a> 


                <a href="{{_url('resturant/'.$meal->resturant_slug.'/'.$meal->menu_section_slug.'/'.$meal->slug)}}" class="more"> <i class="fa fa-angle-left" aria-hidden="true"></i> شاهد التفاصيل</a> 
            </div>
        </div>
        @endforeach
        <!--photbx-->



    </div>
    <!--row-->

    <div class="pager">
        {{ $meals->links() }}  
    </div>
    <!--pager--> 

</div>





@endsection