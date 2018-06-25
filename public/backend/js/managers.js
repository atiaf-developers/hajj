var Managers_grid;

var Managers = function () {
    var init = function () {
        $.extend(lang, new_lang);
        handleRecords();
        handleSubmit();
        handlePasswordActions();

    };

    var handlePasswordActions = function (string_length) {
        $('#show-password').click(function () {
            if ($('#password').val() != '') {
                $("#password").attr("type", "text");

            } else {
                $("#password").attr("type", "password");

            }
        });
        $('#random-password').click(function () {
            $('[id^="password"]').closest('.form-group').removeClass('has-error').addClass('has-success');
            $('[id^="password"]').closest('.form-group').find('.help-block').html('').css('opacity', 0);
            $('[id^="password"]').val(randomPassword(8));
        });
    }
    var randomPassword = function (string_length) {
        var chars = "0123456789!@#$%^&*abcdefghijklmnopqrstuvwxtzABCDEFGHIJKLMNOPQRSTUVWXTZ!@#$%^&*";
        var myrnd = [], pos;
        while (string_length--) {
            pos = Math.floor(Math.random() * chars.length);
            myrnd += chars.substr(pos, 1);
        }
        return myrnd;
    }
    var handleRecords = function () {

        Managers_grid = $('.dataTable').dataTable({
            //"processing": true,
            "serverSide": true,
            "ajax": {
                "url": config.admin_url + "/managers/data",
                "type": "POST",
                data: {_token: $('input[name="_token"]').val()},
            },
            "columns": [
//                    {"data": "user_input", orderable: false, "class": "text-center"},
                {"data": "username"},
                {"data": "active"},
                {"data": "options", orderable: false, searchable: false}
            ],
            "order": [
                [1, "desc"]
            ],
            "oLanguage": {"sUrl": config.url + '/datatable-lang-' + config.lang_code + '.json'}

        });
    }
    var handleSubmit = function () {

        $('#addEditManagersForm').validate({
            rules: {
                username: {
                    required: true
                },
                active: {
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
        $('#addEditManagers .submit-form').click(function () {
            if ($('#addEditManagersForm').validate().form()) {
                $('#addEditManagers .submit-form').prop('disabled', true);
                $('#addEditManagers .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#addEditManagersForm').submit();
                }, 1000);

            }
            return false;
        });
        $('#addEditManagersForm input').keypress(function (e) {
            if (e.which == 13) {
                if ($('#addEditManagersForm').validate().form()) {
                    $('#addEditManagers .submit-form').prop('disabled', true);
                    $('#addEditManagers .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#addEditManagersForm').submit();
                    }, 1000);
                }
                return false;
            }
        });



        $('#addEditManagersForm').submit(function () {
            var id = $('#id').val();
            var formData = new FormData($(this)[0]);
            var action = config.admin_url + '/managers';
            if (id != 0) {
                formData.append('_method', 'PATCH');
                action = config.admin_url + '/managers/' + id;
            }


            $.ajax({
                url: action,
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    $('#addEditManagers .submit-form').prop('disabled', false);
                    $('#addEditManagers .submit-form').html(lang.save);

                    if (data.type == 'success')
                    {
                        My.toast(data.message);
                        Managers_grid.api().ajax.reload();

                        if (id != 0) {
                            $('#addEditManagers').modal('hide');
                        } else {
                            Managers.empty();
                        }

                    } else {
                        console.log(data)
                        if (typeof data.errors === 'object') {
                            for (i in data.errors)
                            {
                                $('[name="' + i + '"]')
                                        .closest('.form-group').addClass('has-error');
                                $('#' + i).closest('.form-group').find(".help-block").html(data.errors[i][0]).css('opacity', 1)
                            }
                        } else {
                            //alert('here');
                            $.confirm({
                                title: lang.error,
                                content: data.message,
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
                        }
                    }
                },
                error: function (xhr, textStatus, errorThrown) {
                    $('#addEditManagers .submit-form').prop('disabled', false);
                    $('#addEditManagers .submit-form').html(lang.save);
                    My.ajax_error_message(xhr);
                },
                dataType: "json",
                type: "POST"
            });

            return false;

        })




    }



    return{
        init: function () {
            init();
        },
        edit: function (t) {
            var id = $(t).attr("data-id");
            My.editForm({
                element: t,
                url: config.admin_url + '/managers/' + id,
                success: function (data)
                {
                    console.log(data);

                    Managers.empty();
                    My.setModalTitle('#addEditManagers', lang.edit_admin);

                    for (i in data.message)
                    {
                        if (i == 'password') {
                            continue;
                        }

                        $('#' + i).val(data.message[i]);
                    }
                    $('#addEditManagers').modal('show');
                }
            });

        },
        delete: function (t) {
            var id = $(t).attr("data-id");
            My.deleteForm({
                element: t,
                url: config.admin_url + '/managers/' + id,
                data: {_method: 'DELETE', _token: $('input[name="_token"]').val()},
                success: function (data)
                {

                    Managers_grid.api().ajax.reload();


                }
            });
        },
        add: function () {
            Managers.empty();
            My.setModalTitle('#addEditManagers', lang.add_admin);
            $('#addEditManagers').modal('show');
        },
        empty: function () {
            $('#id').val(0);
            $('#active').find('option').eq(0).prop('selected', true);
            $('.has-error').removeClass('has-error');
            $('.has-success').removeClass('has-success');
            $('.help-block').html('');
            My.emptyForm();
        },
    };
}();
$(document).ready(function () {
    Managers.init();
});