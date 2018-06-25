var Locations_grid;

var parent_id = 0;

var Locations = function () {

    var init = function () {
        $.extend(lang, new_lang);
        $.extend(config, new_config);
        parent_id = config.parent_id;
        console.log(parent_id);
        //nextLevel = 1;
        handleRecords();
        //handleDatatables();
        handleSubmit();
        
        My.readImageMulti('supervisor_image');
    };

  

    var handleDatatables = function () {
        $(document).on('click', '.data-box', function () {
            parent_id = $(this).data('id');
            level = $(this).data('level');
            var where = $(this).data('where');
            var title = $(this).data('title');
            if (where == 'inTable') {
                nextLevel = level + 1;
            } else {
                nextLevel = level - 1;
            }
            var html = '<a class="panel-title data-box" data-where="outTable" data-id="' + parent_id + '" data-level="' + nextLevel + '"> / ' + title + '</a>';

            if (where == 'inTable') {
                $('.panel-heading').append(html);
            }
            if (where == 'outTable') {
                $(this).nextAll().remove();
            }
            if (typeof Locations_grid === 'undefined') {
                Locations_grid = $('.dataTable').dataTable({
                    //"processing": true,
                    "serverSide": true,
                    "ajax": {
                        "url": config.admin_url + "/locations/data",
                        "type": "POST",
                        data: {parent_id: parent_id, _token: $('input[name="_token"]').val()},
                    },
                    "columns": [
                        {"data": "title"},
                        {"data": "active"},
                        {"data": "this_order"},
                        {"data": "options", orderable: false}
                    ],
                    "order": [
                        [2, "ASC"]
                    ],
                    "oLanguage": {"sUrl": config.url + '/datatable-lang-' + config.lang_code + '.json'}

                });
            } else {
                Locations_grid.on('preXhr.dt', function (e, settings, data) {
                    data.parent_id = parent_id
                    data._token = $('input[name="_token"]').val()
                })
                Locations_grid.api().ajax.url(config.admin_url + "/locations/data").load();
            }


            return false;
        });
    }
    var handleRecords = function () {
        Locations_grid = $('.dataTable').dataTable({
            //"processing": true,
            "serverSide": true,
            "ajax": {
                "url": config.admin_url + "/locations/data",
                "type": "POST",
                data: {parent_id: parent_id, _token: $('input[name="_token"]').val()},
            },
            "columns": [
                {"data": "title","name":"locations_translations.title"},
                {"data": "this_order","name":"locations.this_order"},
                {"data": "options", orderable: false, searchable: false}
            ],
            "order": [
                [2, "asc"]
            ],
            "oLanguage": {"sUrl": config.url + '/datatable-lang-' + config.lang_code + '.json'}

        });
    }


    var handleSubmit = function () {
        $('#addEditLocationsForm').validate({
            rules: {
                 'title[]': {
                     required: true,
                 },
                 prefix: {
                     required: true,
                 },
                 this_order: {
                     required: true,
                 },
                 supervisor_name:{
                     required : true,
                 },
                 supervisor_contact_numbers:{
                     required : true,
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

        $('#addEditLocationsForm .submit-form').click(function () {

            if ($('#addEditLocationsForm').validate().form()) {
                $('#addEditLocationsForm .submit-form').prop('disabled', true);
                $('#addEditLocationsForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#addEditLocationsForm').submit();
                }, 1000);
            }
            return false;
        });
        $('#addEditLocationsForm input').keypress(function (e) {
            if (e.which == 13) {
                if ($('#addEditLocationsForm').validate().form()) {
                    $('#addEditLocationsForm .submit-form').prop('disabled', true);
                    $('#addEditLocationsForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#addEditLocationsForm').submit();
                    }, 1000);
                }
                return false;
            }
        });



        $('#addEditLocationsForm').submit(function () {
            var id = $('#id').val();
            var action = config.admin_url + '/locations';
            var formData = new FormData($(this)[0]);
            formData.append('parent_id',parent_id);
            if (id != 0) {
                formData.append('_method', 'PATCH');
                action = config.admin_url + '/locations/' + id;
            }
            $.ajax({
                url: action,
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    $('#addEditLocationsForm .submit-form').prop('disabled', false);
                    $('#addEditLocationsForm .submit-form').html(lang.save);

                    if (data.type == 'success')
                    {
                        My.toast(data.message);
                        if (id == 0) {
                            Locations.empty();
                        }


                    } else {
                        if (typeof data.errors !== 'undefined') {
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
                                $('[name="' + i + '"]').closest('.form-group').find(".help-block").html(message).css('opacity', 1);
                            }
                        } 
                    }
                },
                error: function (xhr, textStatus, errorThrown) {
                    $('#addEditLocationsForm .submit-form').prop('disabled', false);
                    $('#addEditLocationsForm .submit-form').html(lang.save);
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
        edit: function (t) {
            if (parent_id > 0) {
                $('.for-country').hide();
                $('.for-city').show();
            } else {
                $('.for-country').show();
                $('.for-city').hide();
            }
            var id = $(t).attr("data-id");
            My.editForm({
                element: t,
                url: config.admin_url + '/locations/' + id,
                success: function (data)
                {
                    console.log(data);

                    Locations.empty();
                    My.setModalTitle('#addEditLocationsForm', lang.edit_location);

                    for (i in data.message)
                    {
                        $('#' + i).val(data.message[i]);
                    }
                    $('#addEditLocationsForm').modal('show');
                }
            });

        },
        delete: function (t) {

            var id = $(t).attr("data-id");
            My.deleteForm({
                element: t,
                url: config.admin_url + '/locations/' + id,
                data: {_method: 'DELETE', _token: $('input[name="_token"]').val()},
                success: function (data)
                {
                    Locations_grid.api().ajax.reload();


                }
            });

        },
        add: function () {
            Locations.empty();
            if (parent_id > 0) {
                $('.for-country').hide();
                $('.for-city').show();
            } else {
                $('.for-country').show();
                $('.for-city').hide();
            }

            My.setModalTitle('#addEditLocationsForm', lang.add_location);
            $('#addEditLocationsForm').modal('show');
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
            $('#id').val(0);
            $('#category_icon').val('');
            $('#active').find('option').eq(0).prop('selected', true);
            $('input[type="checkbox"]').prop('checked', false);
            $('.supervisor_image_box').html('<img src="' + config.url + '/no-image.png" class="supervisor_image" width="150" height="80" />');
            $('.has-error').removeClass('has-error');
            $('.has-success').removeClass('has-success');
            $('.help-block').html('');
             $('#contact-numbers-table tbody').html('');
            My.emptyForm();
        }
    };

}();
jQuery(document).ready(function () {
    Locations.init();
});

