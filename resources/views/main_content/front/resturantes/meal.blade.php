@extends('layouts.front')

@section('pageTitle','Ga3aaan')

@section('js')
<script src=" {{ url('public/front/scripts') }}/cart.js"></script>
@endsection



@section('content')

<div class="container">
    <div class="col-sm-12">
        <div class="alert alert-success" style="display:{{Session('successMessage')?'block;':'none;'}}; " role="alert"><i class="fa fa-check" aria-hidden="true"></i> <span class="message">{{Session::get('successMessage')}}</span></div>
        <div class="alert alert-danger" style="display:{{Session('errorMessage')?'block;':'none;'}}; " role="alert"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <span class="message">{{Session::get('errorMessage')}}</span></div>
        <div class="col-sm-7">
            <div class="photos">
                <h2 class="title-serch">الوصف</h2>
                <p class="textcont">{{$meal->description}}</p>
            </div>
            <!--photos-->

            <div class="size">
                @if($meal->sizes->count() > 0)
                <h2 class="title-serch">الحجم</h2>
                <form action="#">
                    <ul class="nameprofile">
                        @foreach($meal->sizes as $key=> $size)
                        @php $qtyName='sqty['.$key.']' @endphp
                        @php $qtyId='sqty'.$size->id @endphp
                        <li>
                            <strong>{{$size->size}}</strong>
                            @if($size->discount_price > 0)
                            <p> {{$size->discount_price.' '.$currency_sign}}  </p>
                            <p style="text-decoration: line-through;"> {{$size->price.' '.$currency_sign}}  </p>
                            @else
                            <p> {{$size->discount_price.' '.$currency_sign}}  </p>
                            @endif
                            <div class="input-group"> 
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default btn-number" data-type="plus" data-field="{{$qtyName}}"> <span class="glyphicon glyphicon-plus"></span> </button>
                                </span>
                                <input type="text" name="{{$qtyName}}" class="form-control input-number" value="1" min="1" max="10">
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default btn-number" disabled="disabled" data-type="minus" data-field="{{$qtyName}}"> <span class="glyphicon glyphicon-minus"></span> </button>
                                </span> 
                            </div>
                            <!--input-group-->

                            <div class="radio radio-info radio-inline">
                                <input id="{{$qtyId}}" {{$key==0?'checked':''}} name="size" value="{{$size->id}}" type="radio">
                                <label for="{{$qtyId}}">
                                </label>
                            </div>
                        </li>
                        @endforeach

                    </ul>
                </form>
                @else
                <div class="input-group"> 
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-default btn-number" data-type="plus" data-field="mqty"> <span class="glyphicon glyphicon-plus"></span> </button>
                    </span>
                    <input type="text" name="mqty" class="form-control input-number" value="1" min="1" max="10">
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-default btn-number" disabled="disabled" data-type="minus" data-field="mqty"> <span class="glyphicon glyphicon-minus"></span> </button>
                    </span> 
                </div>
                @endif
                <div class="sboto">
                    <a href="#" onclick="Cart.addToCart(this);return false;" data-config="{{json_encode($meal->config)}}" class="botoom addadrs basket"><i class="fa fa-shopping-basket" aria-hidden="true"></i> اضف الى السلة</a> 
                </div>

                <!--bolink-->


                <!--bolink-->

                <div id="addToCartModal" class="modal fade" role="dialog">
                    <div class="modal-dialog"> 

                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title titlpop">الاضافات</h4>
                            </div>
                            <form method="post" id="addToCartForm">
                                {{ csrf_field() }}
                                <div class="modal-body">
                                    <ul class="nameprofile">
                                        @foreach($meal->toppings as $key=> $topping)
                                        @php $qtyName='tqty['.$topping->id.']' @endphp
                                        @php $tId='t'.$topping->id @endphp
                                        <li>
                                            <strong>{{$topping->topping}}</strong>
                                            @if($topping->price > 0)
                                            <p>{{$topping->price.' '.$currency_sign}}</p>
                                            @endif
                                            <div class="input-group"> 
                                                <span class="input-group-btn">
                                                    <button type="button" class="btn btn-default btn-number" data-type="plus" data-field="{{$qtyName}}"> <span class="glyphicon glyphicon-plus"></span> </button>
                                                </span>
                                                <input type="text" name="{{$qtyName}}" class="form-control input-number" value="1" min="1" max="10">
                                                <span class="input-group-btn">
                                                    <button type="button" class="btn btn-default btn-number" disabled="disabled" data-type="minus" data-field="{{$qtyName}}"> <span class="glyphicon glyphicon-minus"></span> </button>
                                                </span> 
                                            </div>
                                            <!--input-group-->

                                            <div class="checkbox checkbox-danger">
                                                <input id="{{$tId}}" type="checkbox" name="toppings[]" value="{{$topping->id}}">
                                                <label for="{{$tId}}"> </label>
                                            </div>
                                        </li>
                                        @endforeach

                                    </ul>
                                    <div class="col-sm-12 inputbox">
                                        <textarea class="form-control textarea" name="comment" id="comment" placeholder="التعليق"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer textcent bordno">
                                    <button type="button" class="btn btn-default submit-form">موافق</button>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">الغاء</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!--myModal-->


            </div>
        </div>
        <div class="col-sm-5">
            <div class="imginner"><img src="{{$meal->image}}"></div>
        </div>
        <!--size--> 

    </div>
</div>
<!--container-->





@endsection