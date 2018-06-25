var PilgrimsBuses_grid;

var PilgrimsBuses = function () {

    var init = function () {

        $.extend(config, new_lang);
        handleRecords();
        handleSubmit();
        My.readImageMulti('supervisor_image');

    };


    var handleRecords = function () {
        PilgrimsBuses_grid = $('.dataTable').dataTable({
            "serverSide": true,
            "ajax": {
                "url": config.admin_url + "/pilgrims_buses/data",
                "type": "POST",
                data: {_token: $('input[name="_token"]').val()},
            },
            "columns": [
            {"data": "bus_number","name":"pilgrims_buses.bus_number"},
            {"data": "num_of_seats","name":"pilgrims_buses.num_of_seats"},
            {"data": "name","name":"supervisors.name"},
            {"data": "supervisor_image", orderable: false,searchable: false},
            {"data": "options", orderable: false,searchable: false}
            ],
            
            "oLanguage": {"sUrl": config.url + '/datatable-lang-' + config.lang_code + '.json'}

        });
    }


    var handleSubmit = function () {
        $('#addEditPilgrimsBusesForm').validate({
            rules: {
                location: {
                    required: true,
                },
                bus_number: {
                    required: true,
                },
                this_order: {
                    required: true,
                },
                num_of_seats: {
                    required: true,
                },
                active: {
                    required: true,
                },
                supervisor_name: {
                    required: true,
                },
                supervisor_username: {
                    required: true,
                },
                supervisor_contact_numbers: {
                    required: true,
                },
                supervisor_image: {
                  required: true,
                  extension: "png,gif,jpeg,jpg",
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

       $('#addEditPilgrimsBusesForm .submit-form').click(function () {

        if ($('#addEditPilgrimsBusesForm').validate().form()) {
            $('#addEditPilgrimsBusesForm .submit-form').prop('disabled', true);
            $('#addEditPilgrimsBusesForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
            setTimeout(function () {
                $('#addEditPilgrimsBusesForm').submit();
            }, 1000);
        }
        return false;
    });
       $('#addEditPilgrimsBusesForm input').keypress(function (e) {
        if (e.which == 13) {
            if ($('#addEditPilgrimsBusesForm').validate().form()) {
                $('#addEditPilgrimsBusesForm .submit-form').prop('disabled', true);
                $('#addEditPilgrimsBusesForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#addEditPilgrimsBusesForm').submit();
                }, 1000);
            }
            return false;
        }
    });



       $('#addEditPilgrimsBusesForm').submit(function () {

        var id = $('#id').val();
        var action = config.admin_url + '/pilgrims_buses';
        var formData = new FormData($(this)[0]);
        if (id != 0) {
            formData.append('_method', 'PATCH');
            action = config.admin_url + '/pilgrims_buses/' + id;
        }
        $.ajax({
            url: action,
            data: formData,
            async: false,
            cache: false,
            contentType: false,
            processData: false,
            success: function (data) {
                $('#addEditPilgrimsBusesForm .submit-form').prop('disabled', false);
                $('#addEditPilgrimsBusesForm .submit-form').html(lang.save);

                if (data.type == 'success')
                {
                 My.toast(data.message);
                 if (id == 0) {
                    PilgrimsBuses.empty();
                }
            } else {
                if (typeof data.errors !== 'undefined') {
                    console.log(data.errors);
                    for (i in data.errors)
                    {
                    var message=data.errors[i];
                    $('[name="' + i + '"]')
                    .closest('.form-group').addClass('has-error');
                    $('[name="' + i + '"]').closest('.form-group').find(".help-block").html(message).css('opacity', 1)
                }
            } 
        }
    },
    error: function (xhr, textStatus, errorThrown) {
        $('#addEditPilgrimsBusesForm .submit-form').prop('disabled', false);
        $('#addEditPilgrimsBusesForm .submit-form').html(lang.save);
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
            url: config.admin_url + '/pilgrims_buses/' + id,
            data: {_method: 'DELETE', _token: $('input[name="_token"]').val()},
            success: function (data)
            {
                PilgrimsBuses_grid.api().ajax.reload();
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
            $('.supervisor_image_box').html('<img src="' + config.url + '/no-image.png" class="supervisor_image" width="150" height="80" />');
            $('.has-error').removeClass('has-error');
            $('.has-success').removeClass('has-success');
            $('.help-block').html('');
            My.emptyForm();
        }
    };

}();
jQuery(document).ready(function () {
    PilgrimsBuses.init();
});

