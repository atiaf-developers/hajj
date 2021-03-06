var Buildings_grid;
var lounge_count;
var Buildings = function () {

    var init = function () {

        $.extend(lang, new_lang);
        $.extend(config, new_config);
        lounge_count = $('.lounge-one').length;
        //nextLevel = 1;
        handleRecords();
        handleSubmit();


    };

    var handleRecords = function () {
        Buildings_grid = $('.dataTable').dataTable({
            //"processing": true,
            "serverSide": true,
            "ajax": {
                "url": config.admin_url + "/buildings/data",
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
        $('#addEditBuildingsForm').validate({
            rules: {
                number: {
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



        $('#addEditBuildings .submit-form').click(function () {

            if ($('#addEditBuildingsForm').validate().form()) {
                $('#addEditBuildings .submit-form').prop('disabled', true);
                $('#addEditBuildings .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#addEditBuildingsForm').submit();
                }, 1000);
            }
            return false;
        });
        $('#addEditBuildingsForm input').keypress(function (e) {
            if (e.which == 13) {
                if ($('#addEditBuildingsForm').validate().form()) {
                    $('#addEditBuildings .submit-form').prop('disabled', true);
                    $('#addEditBuildings .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#addEditBuildingsForm').submit();
                    }, 1000);
                }
                return false;
            }
        });



        $('#addEditBuildingsForm').submit(function () {
            var id = $('#id').val();
            var action = config.admin_url + '/buildings';
            var formData = new FormData($(this)[0]);
            if (id != 0) {
                formData.append('_method', 'PATCH');
                action = config.admin_url + '/buildings/' + id;
            }
            $.ajax({
                url: action,
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    $('#addEditBuildings .submit-form').prop('disabled', false);
                    $('#addEditBuildings .submit-form').html(lang.save);

                    if (data.type == 'success') {
                        My.toast(data.message);
                        Buildings_grid.api().ajax.reload();
                        if (id == 0) {
                            Buildings.empty();
                        }else{
                            $('#addEditBuildings').modal('hide');
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
                    $('#addEditBuildings .submit-form').prop('disabled', false);
                    $('#addEditBuildings .submit-form').html(lang.save);
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
                url: config.admin_url + '/buildings/' + id,
                success: function (data) {
                    console.log(data);

                    Buildings.empty();
                    My.setModalTitle('#addEditBuildings', lang.edit);

                    for (i in data.message) {
                        $('#' + i).val(data.message[i]);
                    }
                    $('#addEditBuildings').modal('show');
                }
            });

        },
        delete: function (t) {

            var id = $(t).attr("data-id");
            My.deleteForm({
                element: t,
                url: config.admin_url + '/buildings/' + id,
                data: {_method: 'DELETE', _token: $('input[name="_token"]').val()},
                success: function (data) {
                    Buildings_grid.api().ajax.reload();


                }
            });

        },
        delete_lounge: function (t) {

            var id = $(t).attr("data-id");
            My.deleteForm({
                element: t,
                url: config.admin_url + '/buildings/' + id,
                data: {_method: 'DELETE', _token: $('input[name="_token"]').val()},
                success: function (data) {
                    Buildings_grid.api().ajax.reload();


                }
            });

        },
        add: function () {
            Buildings.empty();
            My.setModalTitle('#addEditBuildings', lang.add);
            $('#addEditBuildings').modal('show');
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
    Buildings.init();
});