@extends('layouts.front')

@section('pageTitle','Ga3aaan')

@section('js')
<script src=" {{ url('public/front/scripts') }}/main.js"></script>
@endsection



@section('content')

<<div class="container">
    <div class="centerbolog">
        <h2 class="title">{{$resturant->title}}</h2>
        <div class="innerboxslin nerboxs"> 
            @if($resturant->is_new)
            <span class="new-bc">{{_lang('app.new')}}</span>
            @endif
            @if($resturant->is_ad)
            <span class="new-bc new-bc2">{{_lang('app.ad')}}</span>
            @endif
            <div class="imgover">
                <img src="{{$resturant->image}}">
                @if(!$resturant->is_open)
                <span class="overlaytext">{{_lang('app.closed')}}</span> 
                @endif
            </div>
            <div class="divtitle">
                <h3 class="nam-tit">{{$resturant->title}}</h3>
                <div class="starbox"> 
                    <span class="namber">({{$resturant->num_of_raters}})</span> 
                    <i class="fa fa-star {{$resturant->rate >= 1?'':'nonbc'}}" aria-hidden="true"></i>
                    <i class="fa fa-star {{$resturant->rate >= 2?'':'nonbc'}}" aria-hidden="true"></i> 
                    <i class="fa fa-star {{$resturant->rate >= 3?'':'nonbc'}}" aria-hidden="true"></i> 
                    <i class="fa fa-star {{$resturant->rate >= 4?'':'nonbc'}}" aria-hidden="true"></i> 
                    <i class="fa fa-star {{$resturant->rate >= 5?'':'nonbc'}}" aria-hidden="true"></i> 
                </div>
                <p class="textblog">
                    @foreach($resturant->cuisines as $key=> $cuisine)
                    {{$cuisine->title}}
                    @if(count($resturant->cuisines) != ($key+1))
                    {{' - '}}
                    @endif
                    @endforeach
                </p>
                <a href="#" style="float:left; color:#d5344a;"><i class="fa fa-info-circle" style="font-size:24px;" aria-hidden="true"></i></a>
            </div>
            <div class="row detbox">
                <div class="boxondet">
                    <p class="colorpris">%</p>
                </div>
                <div class="boxondet">
                    <p>{{_lang('app.delivery_time')}}</p>
                    <span>{{$resturant->delivery_time.' '._lang('app.minute')}}</span> </div>
                <div class="boxondet">
                    <p>{{_lang('app.delivery_cost')}}</p>
                    <span>{{$resturant->delivery_cost.' '.$currency_sign}}</span> </div>
                <div class="boxondet">
                    <p>{{_lang('app.minimum_charge')}}</p>
                    <span>{{$resturant->minimum_charge.' '.$currency_sign}}</span> </div>
            </div>
            <!--innerboxslin--> 

        </div>
        <!--innerboxslin-->

        <div class="orderbasc">
            <ul class="titleprofile basket orderlast">
                <li> 
                    @if($resturant->offer)
                    <a href="#" class="active"> <i class="fa fa-bullhorn" aria-hidden="true"></i>
                        <div class="padline">
                            <p>{{$resturant->offer->offer}}</p>
                            @if($resturant->offer->type==2||$resturant->offer->type==3)
                            <p>{{$resturant->offer->detailes}}</p>
                            @endif
                            <span class="textdeit">تنتهى {{$resturant->offer->valid_until}} </span> </div>
                        <i class="fa fa-chevron-circle-left" aria-hidden="true"></i> 
                    </a>
                    @endif
                </li>
                @foreach($resturant->menu_sections as $key=> $menu_section)
                <li> 
                    <a href="{{_url('resturant/'.$resturant->slug.'/'.$menu_section->slug)}}">
                        <p>{{$menu_section->title}}</p>
                        <i class="fa fa-chevron-circle-left" aria-hidden="true"></i> 
                    </a> 
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    <!--centerbolog--> 

</div>





@endsection