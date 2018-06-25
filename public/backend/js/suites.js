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
    var handleDatatables2 = function () {
        $(document).on('click', '.data-box', function () {
            parent_id = $(this).data('id');
            var box_type = $(this).data('type');
            var title = $(this).data('title');

            if (box_type == 'countries') {
                var html = '<a class="panel-title data-box"  data-type="countries" data-id="0">' + lang.all + '</a>';
                $('.panel-heading').html(html);
                if (!$('#countries_table').hasClass('active')) {
                    $('.table-box').removeClass('active').addClass('disabled');
                    $('#countries_table').removeClass('disabled').addClass('active');

                }
                //$('#rooms_table').addClass('active');
                if (typeof Countries_grid === 'undefined') {
                    Countries_grid = $('#countries_table .dataTable').dataTable({
                        //"processing": true,
                        "serverSide": true,
                        "ajax": {
                            "url": config.admin_url + "/countries/data/?parent_id=" + parent_id,
                            "type": "GET",
                        },
                        "columns": [
//                    {"data": "user_input", orderable: false, "class": "text-center"},
                            {"data": "title_ar"},
                            {"data": "title_en"},
                            {"data": "image"},
                            {"data": "cities"},
                            {"data": "active"},
                            {"data": "this_order"},
                            {"data": "options", orderable: false}
                        ],
                        "order": [
                            [1, "desc"]
                        ],
                        "oLanguage": {"sUrl": config.base_url + '/datatable-lang-' + config.lang_code + '.json'}

                    });
                } else {

                    Countries_grid.api().ajax.url(config.admin_url + "/countries/data/?parent_id=" + parent_id).load();
                }
            }
            if (box_type == 'cities') {
                var html = ' / <a class="panel-title">' + title + '</a>';
                $('.panel-heading').append(html);
                if (!$('#cities_table').hasClass('active')) {
                    $('.table-box').removeClass('active').addClass('disabled');
                    $('#cities_table').removeClass('disabled').addClass('active');

                }
                //$('#rooms_table').addClass('active');
                if (typeof Cities_grid === 'undefined') {
                    Cities_grid = $('#cities_table .dataTable').dataTable({
                        //"processing": true,
                        "serverSide": true,
                        "ajax": {
                            "url": config.admin_url + "/countries/data/?parent_id=" + parent_id,
                            "type": "GET",
                        },
                        "columns": [
//                    {"data": "user_input", orderable: false, "class": "text-center"},
                            {"data": "title_ar"},
                            {"data": "title_en"},
                            {"data": "image"},
                            {"data": "active"},
                            {"data": "this_order"},
                            {"data": "options", orderable: false}
                        ],
                        "order": [
                            [1, "desc"]
                        ],
                        "oLanguage": {"sUrl": config.base_url + '/datatable-lang-' + config.lang_code + '.json'}

                    });
                } else {
                    Cities_grid.api().ajax.url(config.admin_url + "/countries/data/?parent_id=" + parent_id).load();
                }
            }

            return false;
        });
    }

    var handleAddOrRemoveItem = function () {

        $(document).on('click', '.remove-lounge', function () {
            if (config.action == 'edit') {
                var id = $(t).attr("data-id");
                My.deleteForm({
                    element: t,
                    url: config.admin_url + '/suites/' + id + '/delete',
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
                {"data": "gender"},
                {"data": "this_order"},
                {"data": "options", orderable: false, searchable: false}
            ],
            "order": [
                [2, "asc"]
            ],
            "oLanguage": {"sUrl": config.url + '/datatable-lang-' + config.lang_code + '.json'}

        });
    }


    var handleSubmit = function () {
        $('#addEditSuitesForm').validate({
            rules: {
                number: {
                    required: true,
                },
                gender: {
                    required: true,
                },
                this_order: {
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



        $('#addEditSuites .submit-form').click(function () {

            if ($('#addEditSuitesForm').validate().form()) {
                $('#addEditSuites .submit-form').prop('disabled', true);
                $('#addEditSuites .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#addEditSuitesForm').submit();
                }, 1000);
            }
            return false;
        });
        $('#addEditSuitesForm input').keypress(function (e) {
            if (e.which == 13) {
                if ($('#addEditSuitesForm').validate().form()) {
                    $('#addEditSuites .submit-form').prop('disabled', true);
                    $('#addEditSuites .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
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
                    $('#addEditSuites .submit-form').prop('disabled', false);
                    $('#addEditSuites .submit-form').html(lang.save);

                    if (data.type == 'success') {
                        My.toast(data.message);
                        Suites_grid.api().ajax.reload();
                        if (id == 0) {
                            Suites.empty();
                        }else{
                            $('#addEditSuites').modal('hide');
                        }


                    } else {
                        if (typeof data.errors !== 'undefined') {
                            console.log(data.errors);
                            for (i in data.errors) {
                                var message = data.errors[i];

                                $('[name="' + i + '"]')
                                        .closest('.form-group').addClass('has-error');
                                $('[name="' + i + '"]')
                                        .closest('.form-group').find(".help-block").html(message).css('opacity', 1)
                            }
                        }
                    }
                },
                error: function (xhr, textStatus, errorThrown) {
                    $('#addEditSuites .submit-form').prop('disabled', false);
                    $('#addEditSuites .submit-form').html(lang.save);
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

            var id = $(t).attr("data-id");
            My.editForm({
                element: t,
                url: config.admin_url + '/suites/' + id,
                success: function (data) {
                    console.log(data);

                    Suites.empty();
                    My.setModalTitle('#addEditSuites', lang.edit);

                    for (i in data.message) {
                        $('#' + i).val(data.message[i]);
                    }
                    $('#addEditSuites').modal('show');
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
            My.setModalTitle('#addEditSuites', lang.add);
            $('#addEditSuites').modal('show');
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