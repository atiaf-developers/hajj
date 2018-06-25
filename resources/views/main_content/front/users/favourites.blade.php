@extends('layouts.user_profile')
@section('title')
  {{ _lang('app.favourites') }}
@endsection
@section('js')
	<script>
		
         $('#confirm-delete').on('show.bs.modal', function (e) {
            $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
        });

	</script>
@endsection

@section('content')
	

         @foreach ($favourites as $favourite)
          <div class="agent">  

           <a href="#confirm-delete" data-href="{{ route('add-favourite',$favourite->meal_slug) }}" title="{{ _lang('app.delete') }}" data-toggle="modal" class="fa fa-times telibnk">
             
           </a>

        <div class="col-sm-2 titleagent">
          <img src="{{ url('public/uploads/meals/'.$favourite->image) }}">
        </div>
        <div class="col-sm-10 titleagent">

         <a href="{{ _url('resturant/'.$favourite->resturant_slug.'/'.$favourite->menu_section_slug.'/'.$favourite->meal_slug)}}"> <h3 class="nam-tit">{{ $favourite->meal }}</h3></a>

          <p class="textblog">{{ $favourite->resturant }}</p>

          <span class="namber">{{  $favourite->price }} {{ $currency_sign }}</span>
        </div>
        <!--titleagent--> 
        
        </div>
         @endforeach


<div id="confirm-delete" class="modal fade"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"> 
    
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title titlpop">هل انت متأكد من الحذف</h4>
      </div>
      <div class="modal-footer textcent">
        <button type="button" class="btn btn-default" data-dismiss="modal">{{ _lang('app.cancel') }}</button>
        <a class="btn btn-danger btn-ok">{{ trans('messages.delete') }}</a>
      </div>
    </div>
  </div>
</div>

 <div class="pager">
        {{ $favourites->links() }}  
  </div>
		
@endsection