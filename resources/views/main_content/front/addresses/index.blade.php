@extends('layouts.user_profile')
@section('title')
  {{ _lang('app.addresses') }}
@endsection
@section('js')
	<script>
		
         $('#confirm-delete').on('show.bs.modal', function (e) {
            $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
        });

	</script>
@endsection

@section('content')
	<a href="{{ route('user-addresses.create') }}" class="botoom addadrs"><i class="fa fa-plus" aria-hidden="true"></i>{{ _lang('app.add_address') }}</a>

         @foreach ($addresses as $address)
         	<div class="agent"> 

			<a href="#confirm-delete" data-href="{{ route('delete-address',Crypt::encrypt($address->id)) }}" title="{{ _lang('app.delete') }}" data-toggle="modal" class="fa fa-times telibnk"></a>

			 <a href="{{ route('user-addresses.edit',Crypt::encrypt($address->id)) }}" title="{{ _lang('app.edit') }}" class="fa fa-pencil telibnk"></a>

				<div class="col-sm-12 titleagent">

				  <h3 class="nam-tit">{{ $address->city }} - {{ $address->region }}</h3>


				  <p class="textblog">{{ $address->city }} - {{ $address->region }} - {{ $address->sub_region }} - {{ $address->street }}</p>

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

<div class="clearfix"></div>
<div class="pager">
    {{ $addresses->links() }}  
</div>
		
@endsection