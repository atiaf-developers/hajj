var OurLocations_grid;

var OurLocations = function () {

    var init = function () {
 
        $.extend(config, new_lang);
        handleRecords();
        handleSubmit();
        Map.initMap(true,true,true,false);
        My.readImageMulti('location_image');

    };


    var handleRecords = function () {
        OurLocations_grid = $('.dataTable').dataTable({
            "serverSide": true,
            "ajax": {
                "url": config.admin_url + "/our_locations/data",
                "type": "POST",
                data: {_token: $('input[name="_token"]').val()},
            },
            "columns": [
                {"data": "title","name":"our_locations_translations.title"},
                {"data": "location_image", orderable: false,searchable: false},
                {"data": "active","name":"our_locations.active",searchable: false},
                {"data": "this_order","name":"our_locations.this_order"},
                {"data": "options", orderable: false,searchable: false}
            ],
            
            "oLanguage": {"sUrl": config.url + '/datatable-lang-' + config.lang_code + '.json'}

        });
    }


    var handleSubmit = function () {
        $('#addEditOurLocationsForm').validate({
            rules: {
                active: {
                    required: true,
                },
                this_order: {
                    required: true,
                },
                lat: {
                    required: true,
                },
                lng: {
                    required: true,
                },
                contact_numbers: {
                    required: true,
                }
                
            },
            //messages: lang.messages,
            highlight: function (element) { // hightlight error inputs
                $(element).closest('.form-group').removeClass('has-success').addClass('has-error');

            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
                $(element).closest('.form-group').find('.help-block').html('').css('opacity', 0);

            },
            errorPlacement: function (error, element) {
                $(element).closest('.form-group').find('.help-block').html($(error).html()).css('opacity', 1);
            }
        });


        var langs = JSON.parse(config.languages);
        for (var x = 0; x < langs.length; x++) {
             var ele = "input[name='title[" + langs[x] + "]']";
             $(ele).rules('add', {
                 required: true
             });
         }


        $('#addEditOurLocationsForm .submit-form').click(function () {

            if ($('#addEditOurLocationsForm').validate().form()) {
                $('#addEditOurLocationsForm .submit-form').prop('disabled', true);
                $('#addEditOurLocationsForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#addEditOurLocationsForm').submit();
                }, 1000);
            }
            return false;
        });
        $('#addEditOurLocationsForm input').keypress(function (e) {
            if (e.which == 13) {
                if ($('#addEditOurLocationsForm').validate().form()) {
                    $('#addEditOurLocationsForm .submit-form').prop('disabled', true);
                    $('#addEditOurLocationsForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#addEditOurLocationsForm').submit();
                    }, 1000);
                }
                return false;
            }
        });



        $('#addEditOurLocationsForm').submit(function () {
            
            var id = $('#id').val();
            var action = config.admin_url + '/our_locations';
            var formData = new FormData($(this)[0]);
            if (id != 0) {
                formData.append('_method', 'PATCH');
                action = config.admin_url + '/our_locations/' + id;
            }
            $.ajax({
                url: action,
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    $('#addEditOurLocationsForm .submit-form').prop('disabled', false);
                    $('#addEditOurLocationsForm .submit-form').html(lang.save);

                    if (data.type == 'success')
                    {
                       My.toast(data.message);
                        if (id == 0) {
                            OurLocations.empty();
                        }
                    } else {
                        if (typeof data.errors !== 'undefined') {
                            console.log(data.errors);
                            for (i in data.errors)
                            {
                                 var message=data.errors[i];
                                 if (i.startsWith('title')) {
                                    var key_arr = i.split('.');
                                    var key_text = key_arr[0] + '[' + key_arr[1] + ']';
                                    i = key_text;
                                }
                                $('[name="' + i + '"]')
                                        .closest('.form-group').addClass('has-error');
                                $('[name="' + i + '"]').closest('.form-group').find(".help-block").html(message).css('opacity', 1)
                            }
                        } 
                    }
                },
                error: function (xhr, textStatus, errorThrown) {
                    $('#addEditOurLocationsForm .submit-form').prop('disabled', false);
                    $('#addEditOurLocationsForm .submit-form').html(lang.save);
                    My.ajax_error_message(xhr);
                },
                dataType: "json",
                type: "POST"
            });


            return false;

        })




    }

    return {
        init: function () {
            init();
        },
        delete: function (t) {

            var id = $(t).attr("data-id");
            My.deleteForm({
                element: t,
                url: config.admin_url + '/our_locations/' + id,
                data: {_method: 'DELETE', _token: $('input[name="_token"]').val()},
                success: function (data)
                {
                    OurLocations_grid.api().ajax.reload();
                }
            });

        },
    
        error_message: function (message) {
            $.alert({
                title: lang.error,
                content: message,
                type: 'red',
                typeAnimated: true,
                buttons: {
                    tryAgain: {
                        text: lang.try_again,
                        btnClass: 'btn-red',
                        action: function () {
                        }
                    }
                }
            });
        },
        empty: function () {
            //$('#id').val(0);
            $('#category_icon').val('');
            $('#active').find('option').eq(0).prop('selected', true);
            $('input[type="checkbox"]').prop('checked', false);
            $('.location_image_box').html('<img src="' + config.url + '/no-image.png" class="location_image" width="150" height="80" />');
            $('.has-error').removeClass('has-error');
            $('.has-success').removeClass('has-success');
            $('.help-block').html('');
            My.emptyForm();
        }
    };

}();
jQuery(document).ready(function () {
    OurLocations.init();
});

