

var ManualAccommodation = function () {

    var init = function () {

        $.extend(lang, new_lang);
        $.extend(config, new_config);
        handleSubmit();
        handleChangeAccommodationType();
        handleChangePilgrimCode();
        handleChangeBuilding();
        handleChangeFloor();
        handleChangeSuites();
    };
    var handleChangeAccommodationType = function () {
        $('input[name="type_of_accommodation"]').on('change', function () {
            ManualAccommodation.empty();
            var value = $(this).val();
            if (value == 1) {

                $('.suite-item').show();
                $('.building-item').hide();
                $('.tent-item').hide();
                $('.bus-item').hide();
                handleSuitesRules('add');
                handleBuildingsRules('remove');
                handleTentsRules('remove');
                handleBusesRules('remove');

            } else if (value == 2) {

                $('.building-item').show();
                $('.suite-item').hide();
                $('.tent-item').hide();
                $('.bus-item').hide();
                handleBuildingsRules('add');
                handleSuitesRules('remove');
                handleTentsRules('remove');
                handleBusesRules('remove');
            } else if (value == 3) {

                $('.tent-item').show();
                $('.suite-item').hide();
                $('.building-item').hide();
                $('.bus-item').hide();
                handleTentsRules('add');
                handleSuitesRules('remove');
                handleBuildingsRules('remove');
                handleBusesRules('remove');
            }else if (value == 4) {

                $('.bus-item').show();
                $('.tent-item').hide();
                $('.suite-item').hide();
                $('.building-item').hide();
                handleBusesRules('add');
                handleTentsRules('remove');
                handleSuitesRules('remove');
                handleBuildingsRules('remove');
            }
        })
    }
    var handleChangeBuilding = function (ele, suite) {
        $(document).on('change', 'select[name="building"]', function () {
            var building = $(this).val();
            var pilgrim_code = $('#pilgrim_code').val();
            var building = $('select[name="building"]').val();
            var html = '<option value="">' + lang.choose + '</option>';
            if (building && building != '') {
                $.get('' + config.admin_url + '/manual_accommodation/floors?pilgrim_code=' + pilgrim_code + '&building=' + building, function (data) {
                    if (data.data.length != 0)
                    {
                        $.each(data.data, function (index, Obj) {
                            html += '<option value="' + Obj.id + '">' + Obj.number + '</option>';
                        });
                    }
                    $('select[name="floor"]').html(html);

                }, "json");
            } else {
                $('select[name="floor"]').html(html);
            }
        });


    }
    var handleChangeFloor = function (ele, suite) {
        $(document).on('change', 'select[name="floor"]', function () {
            var floor = $(this).val();
            var html = '<option value="">' + lang.choose + '</option>';
            if (building && building != '') {
                $.get('' + config.admin_url + '/manual_accommodation/rooms?floor=' + floor, function (data) {
                    if (data.data.length != 0)
                    {
                        $.each(data.data, function (index, Obj) {
                            html += '<option value="' + Obj.id + '">' + Obj.number + '</option>';
                        });
                    }
                    $('select[name="room"]').html(html);

                }, "json");
            } else {
                $('select[name="room"]').html(html);
            }
        });


    }
    var handleChangeSuites = function (ele, suite) {
        $(document).on('change', 'select[name="suite"]', function () {
            var suite = $(this).val();
            var html = '<option value="">' + lang.choose + '</option>';
            if (building && building != '') {
                $.get('' + config.admin_url + '/manual_accommodation/lounges?suite=' + suite, function (data) {
                    if (data.data.length != 0)
                    {
                        $.each(data.data, function (index, Obj) {
                            html += '<option value="' + Obj.id + '">' + Obj.number + '</option>';
                        });
                    }
                    $('select[name="lounge"]').html(html);

                }, "json");
            } else {
                $('select[name="lounge"]').html(html);
            }
        });


    }
    var handleChangePilgrimCode = function () {
        $('#pilgrim_code').on('change', function () {
            var pilgrim_code = $(this).val();
            var type_of_accommodation = $('input[name="type_of_accommodation"]:checked').val();

            if (pilgrim_code && pilgrim_code != '') {
                $('.loader').show();
                setTimeout(function () {
                    $.ajax({
                        url: config.admin_url + '/manual_accommodation/getDataForAccommodation',
                        data: {pilgrim_code: pilgrim_code, type_of_accommodation: type_of_accommodation, _token: $('input[name="_token"]').val()},
                        async: false,
                        success: function (data) {
                            console.log(data);
                            $('.loader,.loading').hide();
                            var html = '<option value="">' + lang.choose + '</option>'
                            $.each(data.data, function (index, Obj) {

                                html += '<option value="' + Obj.id + '">' + Obj.number + '</option>';
                            });
                            if (type_of_accommodation == 1) {
                                $('select[name="suite"]').html(html);
                            } else if (type_of_accommodation == 2) {
                                $('select[name="building"]').html(html);
                            } else if (type_of_accommodation == 3) {
                                $('select[name="tent"]').html(html);
                            }else if (type_of_accommodation == 4) {
                                $('select[name="bus"]').html(html);
                            }

                        },
                        error: function (xhr, textStatus, errorThrown) {
                            $('.loader').hide();
                            My.ajax_error_message(xhr);
                        },
                        dataType: "json",
                        type: "POST"
                    });
                }, 1000);


            }
        })
    }

    var handleSuitesRules = function (type) {
        if (type == 'add') {
            $('select[name="suite"]').rules('add', {
                required: true
            });
            $('select[name="lounge"]').rules('add', {
                required: true
            });

        } else {
            $('select[name="suite"]').rules('remove', 'required');
            $('select[name="lounge"]').rules('remove', 'required');

        }

    }
    var handleBuildingsRules = function (type) {
        if (type == 'add') {
            $('select[name="building"]').rules('add', {
                required: true
            });
            $('select[name="floor"]').rules('add', {
                required: true
            });
            $('select[name="room"]').rules('add', {
                required: true
            });

        } else {
            $('select[name="building"]').rules('remove', 'required');
            $('select[name="floor"]').rules('remove', 'required');
            $('select[name="room"]').rules('remove', 'required');

        }

    }
    var handleTentsRules = function (type) {
        if (type == 'add') {
            $('select[name="tent"]').rules('add', {
                required: true
            });
        } else {
            $('select[name="tent"]').rules('remove', 'required');
        }

    }
    var handleBusesRules = function (type) {
        if (type == 'add') {
            $('select[name="bus"]').rules('add', {
                required: true
            });
        } else {
            $('select[name="bus"]').rules('remove', 'required');
        }

    }


    var handleSubmit = function () {


        $('#addEditManualAccommodationForm').validate({
            rules: {
                pilgrim_code: {
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

        handleSuitesRules('add');



        $('#addEditManualAccommodationForm .submit-form').click(function () {

            if ($('#addEditManualAccommodationForm').validate().form()) {
                $('#addEditManualAccommodationForm .submit-form').prop('disabled', true);
                $('#addEditManualAccommodationForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#addEditManualAccommodationForm').submit();
                }, 1000);
            }
            return false;
        });
        $('#addEditManualAccommodationForm input').keypress(function (e) {
            if (e.which == 13) {
                if ($('#addEditManualAccommodationForm').validate().form()) {
                    $('#addEditManualAccommodationForm .submit-form').prop('disabled', true);
                    $('#addEditManualAccommodationForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#addEditManualAccommodationForm').submit();
                    }, 1000);
                }
                return false;
            }
        });



        $('#addEditManualAccommodationForm').submit(function () {
            var id = $('#id').val();
            var action = config.admin_url + '/manual_accommodation';
            var formData = new FormData($(this)[0]);

            $.ajax({
                url: action,
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    console.log(data);
                    $('#addEditManualAccommodationForm .submit-form').prop('disabled', false);
                    $('#addEditManualAccommodationForm .submit-form').html(lang.save);

                    if (data.type == 'success')
                    {
                        My.toast(data.message);
                        var type_of_accommodation = $('input[name="type_of_accommodation"]:checked').val();

                        setTimeout(function () {
                            if (type_of_accommodation == 1) {
                                window.location.href = config.admin_url + "/suites_accommodation";
                            } else if (type_of_accommodation == 2) {
                                window.location.href = config.admin_url + "/buildings_accommodation";
                            } else if (type_of_accommodation == 3) {
                                window.location.href = config.admin_url + "/tents_accommodation";
                            }else if (type_of_accommodation == 4) {
                                window.location.href = config.admin_url + "/buses_accommodation";
                            }

                        }, 1500);


                    } else {
                        if (typeof data.errors !== 'undefined') {
                            for (i in data.errors)
                            {
                                var message = data.errors[i];
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
                    $('#addEditManualAccommodationForm .submit-form').prop('disabled', false);
                    $('#addEditManualAccommodationForm .submit-form').html(lang.save);
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
                url: config.admin_url + '/categories/' + id,
                success: function (data)
                {
                    console.log(data);

                    ManualAccommodation.empty();
                    My.setModalTitle('#addEditManualAccommodation', lang.edit);

                    for (i in data.message)
                    {
                        $('#' + i).val(data.message[i]);
                    }
                    $('#addEditManualAccommodation').modal('show');
                }
            });

        },
        delete: function (t) {

            var id = $(t).attr("data-id");
            My.deleteForm({
                element: t,
                url: config.admin_url + '/categories/' + id,
                data: {_method: 'DELETE', _token: $('input[name="_token"]').val()},
                success: function (data)
                {
                    ManualAccommodation_grid.api().ajax.reload();
                }
            });

        },
        add: function () {
            ManualAccommodation.empty();
            My.setModalTitle('#addEditManualAccommodation', lang.add);
            $('#addEditManualAccommodation').modal('show');
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
            $('#pilgrim_code').val('');
            $('select:not(select[name="buildings_accommodation_type"],select[name="suites_accommodation_type"])').html('<option value="">' + lang.choose + '</option>');
            $('.loading').show();
            $('.has-error').removeClass('has-error');
            $('.has-success').removeClass('has-success');
            $('.help-block').html('');
            My.emptyForm();
        }
    };

}();
jQuery(document).ready(function () {
    ManualAccommodation.init();
});

