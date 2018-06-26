var Pilgrims_grid;
var created_at;
var Pilgrims = function () {

    var init = function () {
        $.extend(lang, new_lang);
        $.extend(config, new_config);
        handleRecords();
        handleSubmit();
        handleImportSubmit();
        handleGenerateQrCode();
        My.readImageMulti('image');
    };

    var handleGenerateQrCode = function () {
        $(document).on('click', '.btn-qr', function () {
            var $this = $(this);
            $this.prop('disabled', true);
            $this.html('<i class="fa fa-spin fa-spinner"></i>');
            setTimeout(function () {
                $.ajax({
                    url: config.admin_url + "/pilgrims/generate_qr",
                    data: {created_at: created_at, _token: $('input[name="_token"]').val()},
                    success: function (data) {
                        $this.prop('disabled', false);
                        $this.html(lang.generate_qr_code);
                        console.log(data);
                        if (data.type == 'success') {

                            My.toast(data.message);
                            Pilgrims_grid.api().ajax.reload();
                            $('#importPilgrims').modal('hide');

                        } else {

                        }
                    },
                    error: function (xhr, textStatus, errorThrown) {
                        $this.prop('disabled', false);
                        $this.html(lang.generate_qr_code);
                        My.ajax_error_message(xhr);
                    },
                    dataType: "json",
                    type: "post"
                })
            }, 200);
        })

    }
    var handleRecords = function () {
        Pilgrims_grid = $('.dataTable').dataTable({
            //"processing": true,
            "serverSide": true,
            "ajax": {
                "url": config.admin_url + "/pilgrims/data",
                "type": "POST",
                data: {_token: $('input[name="_token"]').val()},
            },
            "columns": [
//                    {"data": "user_input", orderable: false, "class": "text-center"},
                {"data": "name", name: "pilgrims.name"},
                {"data": "ssn", name: "pilgrims.ssn"},
                {"data": "reservation_no", name: "pilgrims.reservation_no"},
                {"data": "code", name: "pilgrims.code"},
                {"data": "title", name: "trans.title"},
                {"data": "active"},
                {"data": "accommodation_status", orderable: false, searchable: false},
                {"data": "options", orderable: false, searchable: false}
            ],
            "order": [
                [2, "desc"]
            ],
            "oLanguage": {"sUrl": config.url + '/datatable-lang-' + config.lang_code + '.json'}

        });
    }


    var handleSubmit = function () {


        $('#addEditPilgrimsForm').validate({
            ignore: "",
            rules: {
                name: {
                    required: true,
                },
                nationality: {
                    required: true,
                },
                ssn: {
                    required: true,
                },
                mobile: {
                    required: true,
                },
                reservation_no: {
                    required: true,
                },
                location: {
                    required: true,
                },
                pilgrim_class: {
                    required: true,
                },
                gender: {
                    required: true,
                },
                image: {
                    accept: "image/*",
                    filesize: 1000 * 1024
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


        $('#addEditPilgrims .submit-form').click(function () {

            if ($('#addEditPilgrimsForm').validate().form()) {
                $('#addEditPilgrims .submit-form').prop('disabled', true);
                $('#addEditPilgrims .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#addEditPilgrimsForm').submit();
                }, 1000);
            }
            return false;
        });
        $('#addEditPilgrimsForm input').keypress(function (e) {
            if (e.which == 13) {
                if ($('#addEditPilgrimsForm').validate().form()) {
                    $('#addEditPilgrims .submit-form').prop('disabled', true);
                    $('#addEditPilgrims .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#addEditPilgrimsForm').submit();
                    }, 1000);
                }
                return false;
            }
        });



        $('#addEditPilgrimsForm').submit(function () {
            var id = $('#id').val();
            var action = config.admin_url + '/pilgrims';
            var formData = new FormData($(this)[0]);
            if (id != 0) {
                formData.append('_method', 'PATCH');
                action = config.admin_url + '/pilgrims/' + id;
            }
            $.ajax({
                url: action,
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    console.log(data);
                    $('#addEditPilgrims .submit-form').prop('disabled', false);
                    $('#addEditPilgrims .submit-form').html(lang.save);

                    if (data.type == 'success')
                    {
                        My.toast(data.message);

                        Pilgrims_grid.api().ajax.reload();
                        if (id != 0) {
                            $('#addEditPilgrims').modal('hide');
                        } else {

                            Pilgrims.empty();
                        }


                    } else {
                        if (typeof data.errors !== 'undefined') {
                            for (i in data.errors)
                            {
                                var message = data.errors[i][0];
                                i = '[name="' + i + '"]';

                                $(i)
                                        .closest('.form-group').addClass('has-error');
                                $(i).closest('.form-group').find(".help-block").html(message).css('opacity', 1)
                            }
                        }
                    }
                },
                error: function (xhr, textStatus, errorThrown) {
                    $('#addEditPilgrims .submit-form').prop('disabled', false);
                    $('#addEditPilgrims .submit-form').html(lang.save);
                    My.ajax_error_message(xhr);
                },
                dataType: "json",
                type: "POST"
            });


            return false;

        })




    }
    var handleImportSubmit = function () {


        $('#importPilgrimsForm').validate({
            rules: {
                location: {
                    required: true,
                },
                pilgrim_class: {
                    required: true,
                },
                gender: {
                    required: true,
                },
                file: {
                    required: true,
                    extension: "csv"
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

        $('#importPilgrims .submit-form').click(function () {

            if ($('#importPilgrimsForm').validate().form()) {
                $('#importPilgrims .submit-form').prop('disabled', true);
                $('#importPilgrims .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#importPilgrimsForm').submit();
                }, 1000);
            }
            return false;
        });
        $('#importPilgrimsForm input').keypress(function (e) {
            if (e.which == 13) {
                if ($('#importPilgrimsForm').validate().form()) {
                    $('#importPilgrims .submit-form').prop('disabled', true);
                    $('#importPilgrims .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#importPilgrimsForm').submit();
                    }, 1000);
                }
                return false;
            }
        });



        $('#importPilgrimsForm').submit(function () {
            var action = config.admin_url + '/pilgrims/import';
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
                    $('#importPilgrims .submit-form').prop('disabled', false);
                    $('#importPilgrims .submit-form').html(lang.save);

                    if (data.type == 'success')
                    {
                        var qrButton = ' <div style="height:180px;">' +
                                '<a href="javascript:;" class="icon-btn btn-qr">' +
                                '<i class="fa fa-thumbs-up"></i>' +
                                '<div>' + lang.generate_qr_code + '</div>' +
                                '</a>' +
                                '</div>';

                        $('#importPilgrimsForm .form-body').empty();
                        $('#importPilgrimsForm .form-body').html(qrButton);
                        created_at = data.message;
//                        My.toast(data.message);
//                        Pilgrims_grid.api().ajax.reload();
//                        $('#importPilgrims').modal('hide');


                    } else {
                        if (typeof data.errors !== 'undefined') {
                            for (i in data.errors)
                            {
                                var message = data.errors[i][0];
                                i = '[name="' + i + '"]';

                                $(i)
                                        .closest('.form-group').addClass('has-error');
                                $(i).closest('.form-group').find(".help-block").html(message).css('opacity', 1)
                            }
                        }
                    }
                },
                error: function (xhr, textStatus, errorThrown) {
                    $('#importPilgrims .submit-form').prop('disabled', false);
                    $('#importPilgrims .submit-form').html(lang.save);
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
        generateQr: function (t) {


        },
        edit: function (t) {


            var id = $(t).attr("data-id");
            My.editForm({
                element: t,
                url: config.admin_url + '/pilgrims/' + id+'/edit',
                success: function (data)
                {
                    console.log(data);

                    Pilgrims.empty();
                    My.setModalTitle('#addEditPilgrims', lang.edit);

                    for (i in data.message)
                    {
                        if (i == 'location_id') {
                            $('#location').val(data.message[i]);
                        } else if (i == 'pilgrim_class_id') {
                            $('#pilgrim_class').val(data.message[i]);
                        } else if (i == 'image') {
                            $('.image_box').html('<img src="' + config.url + '/public/uploads/pilgrims/' + data.message[i] + '" class="image" width="150" height="80" />');
                        } else {
                            $('#' + i).val(data.message[i]);
                        }

                    }
                    $('#addEditPilgrims').modal('show');
                }
            });

        },
        delete: function (t) {

            var id = $(t).attr("data-id");
            My.deleteForm({
                element: t,
                url: config.admin_url + '/pilgrims/' + id,
                data: {_method: 'DELETE', _token: $('input[name="_token"]').val()},
                success: function (data)
                {
                    Pilgrims_grid.api().ajax.reload();
                }
            });

        },
        add: function () {

            Pilgrims.empty();
            My.setModalTitle('#addEditPilgrims', lang.add);
            $('#addEditPilgrims').modal('show');

        },
        import: function () {
            Pilgrims.empty();
            My.setModalTitle('#importPilgrims', lang.import);
            $('#importPilgrims').modal('show');
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
            $('select').val('');
            $('input[type="file"]').val('');
            $('input[type="checkbox"]').prop('checked', false);
            $('.image_box').html('<img src="' + config.url + '/no-image.png" class="image" width="150" height="80" />');
            $('.has-error').removeClass('has-error');
            $('.has-success').removeClass('has-success');
            $('.help-block').html('');
            My.emptyForm();
        }
    };

}();
jQuery(document).ready(function () {
    Pilgrims.init();
});

