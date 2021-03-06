<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="stylesheet" href="{{url('public/front/css')}}/bootstrap.min.css" />
<link rel="stylesheet" href="{{url('public/front/css')}}/bootstrap-rtl.css" />
<link rel="stylesheet" href="{{url('public/front/css')}}/style.css" />
<link rel="stylesheet" href="{{url('public/front/css')}}/my.css" />
<link rel="stylesheet" href="{{url('public/front/css')}}/settings.css" media="screen" />
<!--<link rel="stylesheet" href="{{url('public/front/css')}}/bootstrap-select.min.css" type="text/css">-->
<link rel="stylesheet" href="{{url('public/front/css')}}/intlTelInput.css">
<link rel="stylesheet" type="text/css" href="{{url('public/front/css')}}/font-awesome.min.css">
<link rel="icon" href="{{url('public/front/images')}}/favicon.png" type="image/png">
<script>var SITE_URL = 'http://wowslider.com/';</script>

<title>@yield('pageTitle')</title>


<script>
    var config = {
        url: " {{ _url('') }}",
        customer_url: " {{ _url('customer') }}",
        lang: "{{ $lang_code }}",
        lang_code: "{{ $lang_code }}",
        isUser: "{{ $isUser }}"
    };


      var lang = {
        currency_sign: "{{ $currency_sign }}",
        order_is_deleted: "{{ _lang('app.order_is_deleted') }}",
        save: "{{ _lang('app.save') }}",
        request_sent_successfully: "{{ _lang('app.request_sent_successfully') }}",
        you_must_login_first: "{{ _lang('app.you_must_login_first') }}",
        view_resturantes: "{{ _lang('app.view_resturantes') }}",
        region: "{{ _lang('app.region') }}",
        next: "{{ _lang('app.next') }}",
        confirm : "{{ _lang('app.confirm') }}",
        send_request: "{{ _lang('app.send_request') }}",
        choose: "{{ _lang('app.choose') }}",
        login: "{{ _lang('app.login') }}",
        register: "{{ _lang('app.register') }}",
        delete: "{{ _lang('app.delete')}}",
        view_message: "{{ _lang('app.view_message')}}",
        send: "{{ _lang('app.send')}}",
        no_results: "{{ _lang('app.no_results')}}",
        cancel_change_category: "{{ _lang('app.cancel_change_category')}}",
        change_category: "{{ _lang('app.change_category')}}",
        countries: "{{ _lang('app.countries')}}",
        no_category_selected: "{{ _lang('app.no_category_selected')}}",
        active: "{{ _lang('app.active')}}",
        not_active: "{{ _lang('app.not_active')}}",
        close: "{{ _lang('app.close')}}",
        no_item_selected: "{{ _lang('app.no_item_selected')}}",
        add_user: "{{ _lang('app.add_user')}}",
        edit_user: "{{ _lang('app.edit_user')}}",
        add_group: "{{ _lang('app.add_group')}}",
        edit_group: "{{ _lang('app.edit_group')}}",
        add_country: "{{ _lang('app.add_country')}}",
        edit_country: "{{ _lang('app.edit_country')}}",
        add_company: "{{ _lang('app.add_company')}}",
        edit_company: "{{ _lang('app.edit_company')}}",
        add_program_category: "{{ _lang('app.add_program_category')}}",
        edit_program_category: "{{ _lang('app.edit_program_category')}}",
        add_airlines: "{{ _lang('app.add_airlines')}}",
        edit_airlines: "{{ _lang('app.edit_airlines')}}",
        add_flight_bookings: "{{ _lang('app.add_flight_bookings')}}",
        edit_flight_bookings: "{{ _lang('app.edit_flight_bookings')}}",
        add_hotel_room: "{{ _lang('app.add_hotel_room')}}",
        edit_hotel_room: "{{ _lang('app.edit_hotel_room')}}",
        add_hotel_advantages: "{{ _lang('app.add_hotel_advantages')}}",
        edit_hotel_advantages: "{{ _lang('app.edit_hotel_advantages')}}",
        add_hotel_extra_services: "{{ _lang('app.add_hotel_extra_services')}}",
        edit_hotel_extra_services: "{{ _lang('app.edit_hotel_extra_services')}}",
        add_hotel_room_meals: "{{ _lang('app.add_hotel_room_meals')}}",
        edit_hotel_room_meals: "{{ _lang('app.edit_hotel_room_meals')}}",
        add_hotel: "{{ _lang('app.add_hotel')}}",
        edit_hotel: "{{ _lang('app.edit_hotel')}}",
        add_currency: "{{ _lang('app.add_currency')}}",
        edit_currency: "{{ _lang('app.edit_currency')}}",
        save: "{{ _lang('app.save')}}",
        updated_successfully: "{{ _lang('app.updated_successfully')}}",
        loading: "{{ _lang('app.loading')}}",
        deleting: "{{ _lang('app.deleting')}}",
        delete: "{{ _lang('app.delete')}}",
        uploading: "{{ _lang('app.uploading')}}",
        upload: "{{ _lang('app.upload')}}",
        required: "{{ _lang('app.this_field_is_required')}}",
        email_not_valid: "{{ _lang('app.email_is_not_valid')}}",
        alert_message: "{{ _lang('app.alert_message')}}",
        confirm_message_title: "{{ _lang('app.are you sure !?')}}",
        deleting_cancelled: "{{ _lang('app.deleting_cancelled')}}",
        yes: "{{ _lang('app.yes')}}",
        no: "{{ _lang('app.no')}}",
        error: "{{ _lang('app.error')}}",
        try_again: "{{ _lang('app.try_again')}}",
        choose_one: "{{ _lang('app.please_choose_one')}}",
        no_file_to_upload: "{{ _lang('app.no_file_to_upload')}}",
    };

    var errorElements1 = new Array;
    var errorElements2 = new Array;
    var errorElements = new Array;
   
</script>