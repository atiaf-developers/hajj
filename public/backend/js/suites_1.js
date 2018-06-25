var Suites_grid;
var lounge_count;
var Suites = function () {

    var init = function () {

        $.extend(lang, new_lang);
        $.extend(config, new_config);
        lounge_count = $('.lounge-one').length;
        //nextLevel = 1;
        handleRecords();
        handleAddOrRemoveItem();
        handleSubmit();


    };

    var handleAddOrRemoveItem = function () {

        $(document).on('click', '.remove-lounge', function () {
            if (config.action == 'edit') {
                var id = $(t).attr("data-id");
                My.deleteForm({
                    element: t,
                    url: config.admin_url + '/suites/' + id+'/delete',
                    data: {_method: 'DELETE', _token: $('input[name="_token"]').val()},
                    success: function (data) {
                        Suites_grid.api().ajax.reload();


                    }
                });
            } else {
                $(this).closest('tr').remove();
                lounge_count--;
            }


        });
        $('.add-lounge').on('click', function () {
            var html = '<tr class="lounge-one">' +
                    '<td>' +
                    '<div class="form-group form-md-line-input">' +
                    '<input type="number" class="form-control"  name="lounge_number[' + lounge_count + ']" value="">' +
                    '<label for="">' + lang.lounge_number + '</label>' +
                    '<span class="help-block"></span>' +
                    '</div>' +
                    '</td>';


            html += '<td>' +
                    '<div class="form-group form-md-line-input">' +
                    '<input type="number" class="form-control"  name="available_of_accommodation[' + lounge_count + ']" value="">' +
                    '<label for="">' + lang.available_of_accommodation + '</label>' +
                    '<span class="help-block"></span>' +
                    '</div>' +
                    '</td>';

            html += '<td>' +
                    '<div class="form-group form-md-line-input">' +
                    '<select class="form-control edited" name="gender[' + lounge_count + ']">' +
                    '<option  value="">' + lang.choose + '</option>' +
                    '<option  value="1">' + lang.male + '</option>' +
                    '<option  value="2">' + lang.female + '</option>' +
                    '</select>' +
                    '<label for="">' + lang.gender + '</label>' +
                    '<span class="help-block"></span>' +
                    '</div>' +
                    '</td>';

            html += '<td>' +
                    '<a class="btn btn-danger remove-lounge">' + lang.remove + '</a></td>' +
                    '</tr>';


            $('#lounge-table tbody').append(html);
            var lounge_number_ele = "input[name='lounge_number[" + lounge_count + "]']";
            var available_of_accommodation_ele = "input[name='available_of_accommodation[" + lounge_count + "]']";
            var gender_ele = "select[name='gender[" + lounge_count + "]']";
            $(lounge_number_ele).rules('add', {
                required: true
            });
            $(available_of_accommodation_ele).rules('add', {
                required: true
            });
            $(gender_ele).rules('add', {
                required: true
            });
            lounge_count++;
        });
    }



    var handleRecords = function () {
        Suites_grid = $('.dataTable').dataTable({
            //"processing": true,
            "serverSide": true,
            "ajax": {
                "url": config.admin_url + "/suites/data",
                "type": "POST",
                data: {_token: $('input[name="_token"]').val()},
            },
            "columns": [
                {"data": "number"},
                {"data": "this_order"},
                {"data": "options", orderable: false, searchable: false}
            ],
            "order": [
                [1, "asc"]
            ],
            "oLanguage": {"sUrl": config.url + '/datatable-lang-' + config.lang_code + '.json'}

        });
    }


    var handleSubmit = function () {
        jQuery.validator.addMethod("unique", function (value, element, params) {
            var prefix = params;
            var selector = jQuery.validator.format("[name!='{0}'][unique='{1}']", element.name, prefix);
            var selector = 'input[name^=lounge_number]';
            console.log(element.name);
            var matches = new Array();
            $(selector).each(function (index, item) {
                if (element.name != item.name && value == $(item).val()) {
                    matches.push(item);
                }
            });
            console.log(matches);
            return matches.length == 0;
        }, "Value is not unique.");
        $('#addEditSuitesForm').validate({
            rules: {
                suite_number: {
                    required: true,
                },
                this_order: {
                    required: true,
                },
                lounge_number: {
                    unique: true,
                },
                'lounge_number[0]': {
                    required: true,
                    //unique: true,
                },
                'available_of_accommodation[0]': {
                    required: true,
                },
                'gender[0]': {
                    required: true,
                },

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

        if (config.action == 'edit') {
            var lounge_number_length = $("input[name^='lounge_number']").length;
            for (var x = 0; x < lounge_number_length; x++) {
                var lounge_number_ele = "input[name='lounge_number[" + x + "]']";
                var available_of_accommodation_ele = "input[name='available_of_accommodation[" + x + "]']";
                var gender_ele = "select[name='gender[" + x + "]']";
                $(lounge_number_ele).rules('add', {
                    required: true,
                    //unique: true,
                });
                $(available_of_accommodation_ele).rules('add', {
                    required: true
                });
                $(gender_ele).rules('add', {
                    required: true
                });
            }

        }


        $('#addEditSuitesForm .submit-form').click(function () {

            if ($('#addEditSuitesForm').validate().form()) {
                $('#addEditSuitesForm .submit-form').prop('disabled', true);
                $('#addEditSuitesForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#addEditSuitesForm').submit();
                }, 1000);
            }
            return false;
        });
        $('#addEditSuitesForm input').keypress(function (e) {
            if (e.which == 13) {
                if ($('#addEditSuitesForm').validate().form()) {
                    $('#addEditSuitesForm .submit-form').prop('disabled', true);
                    $('#addEditSuitesorm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#addEditSuitesForm').submit();
                    }, 1000);
                }
                return false;
            }
        });



        $('#addEditSuitesForm').submit(function () {
            var id = $('#id').val();
            var action = config.admin_url + '/suites';
            var formData = new FormData($(this)[0]);
            if (id != 0) {
                formData.append('_method', 'PATCH');
                action = config.admin_url + '/suites/' + id;
            }
            $.ajax({
                url: action,
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    $('#addEditSuitesForm .submit-form').prop('disabled', false);
                    $('#addEditSuitesForm .submit-form').html(lang.save);

                    if (data.type == 'success') {
                        My.toast(data.message);
                        Suites_grid.api().ajax.reload();
                        if (id == 0) {
                            Suites.empty();
                        }


                    } else {
                        if (typeof data.errors !== 'undefined') {
                            console.log(data.errors);
                            for (i in data.errors) {
                                var message = data.errors[i];
                                if (i.startsWith('lounge_number') || i.startsWith('available_of_accommodation') || i.startsWith('gender')) {
                                    var key_arr = i.split('.');
                                    var key_text = key_arr[0] + '[' + key_arr[1] + ']';
                                    i = key_text;
                                }
                                console.log(i);
                                $('[name="' + i + '"]')
                                        .closest('.form-group').addClass('has-error');
                                $('[name="' + i + '"]')
                                        .closest('.form-group').find(".help-block").html(message).css('opacity', 1)
                            }
                        }
                    }
                },
                error: function (xhr, textStatus, errorThrown) {
                    $('#addEditSuitesForm .submit-form').prop('disabled', false);
                    $('#addEditSuitesForm .submit-form').html(lang.save);
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
                url: config.admin_url + '/suites/' + id,
                success: function (data) {
                    console.log(data);

                    Suites.empty();
                    My.setModalTitle('#addEditSuitesForm', lang.edit_Common);

                    for (i in data.message) {
                        $('#' + i).val(data.message[i]);
                    }
                    $('#addEditSuitesForm').modal('show');
                }
            });

        },
        delete: function (t) {

            var id = $(t).attr("data-id");
            My.deleteForm({
                element: t,
                url: config.admin_url + '/suites/' + id,
                data: {_method: 'DELETE', _token: $('input[name="_token"]').val()},
                success: function (data) {
                    Suites_grid.api().ajax.reload();


                }
            });

        },
        delete_lounge: function (t) {

            var id = $(t).attr("data-id");
            My.deleteForm({
                element: t,
                url: config.admin_url + '/suites/' + id,
                data: {_method: 'DELETE', _token: $('input[name="_token"]').val()},
                success: function (data) {
                    Suites_grid.api().ajax.reload();


                }
            });

        },
        add: function () {
            Suites.empty();
            if (parent_id > 0) {
                $('.for-country').hide();
                $('.for-city').show();
            } else {
                $('.for-country').show();
                $('.for-city').hide();
            }

            My.setModalTitle('#addEditSuitesForm', lang.add_Common);
            $('#addEditSuitesForm').modal('show');
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
                        action: function () {}
                    }
                }
            });
        },
        empty: function () {
            $('#id').val(0);
            $('#category_icon').val('');
            $('#active').find('option').eq(0).prop('selected', true);
            $('input[type="checkbox"]').prop('checked', false);
            $('.image_uploaded_box').html('<img src="' + config.base_url + 'no-image.png" class="category_icon" width="150" height="80" />');
            $('.has-error').removeClass('has-error');
            $('.has-success').removeClass('has-success');
            $('.help-block').html('');
            My.emptyForm();
        }
    };

}();
jQuery(document).ready(function () {
    Suites.init();
});