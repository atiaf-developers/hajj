var BuildingsFloors_grid;
var building_id;
var BuildingsFloors = function () {

    var init = function () {

        $.extend(lang, new_lang);
        $.extend(config, new_config);
        building_id=config.building_id;
        //console.log(building_id);
        handleRecords();

        handleSubmit();


    };





    var handleRecords = function () {
        BuildingsFloors_grid = $('.dataTable').dataTable({
            //"processing": true,
            "serverSide": true,
            "ajax": {
                "url": config.admin_url + "/buildings_floors/data",
                "type": "POST",
                data: {building: building_id, _token: $('input[name="_token"]').val()},
            },
            "columns": [
                {"data": "number"},
                {"data": "gender"},
                {"data": "options", orderable: false, searchable: false}
            ],
            "order": [
                [0, "asc"]
            ],
            "oLanguage": {"sUrl": config.url + '/datatable-lang-' + config.lang_code + '.json'}

        });
    }


    var handleSubmit = function () {
        $('#addEditBuildingsFloorsForm').validate({
            rules: {
                number: {
                    required: true,
                },
                gender: {
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



        $('#addEditBuildingsFloors .submit-form').click(function () {

            if ($('#addEditBuildingsFloorsForm').validate().form()) {
                $('#addEditBuildingsFloors .submit-form').prop('disabled', true);
                $('#addEditBuildingsFloors .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#addEditBuildingsFloorsForm').submit();
                }, 1000);
            }
            return false;
        });
        $('#addEditBuildingsFloorsForm input').keypress(function (e) {
            if (e.which == 13) {
                if ($('#addEditBuildingsFloorsForm').validate().form()) {
                    $('#addEditBuildingsFloors .submit-form').prop('disabled', true);
                    $('#addEditBuildingsFloors .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#addEditBuildingsFloorsForm').submit();
                    }, 1000);
                }
                return false;
            }
        });



        $('#addEditBuildingsFloorsForm').submit(function () {
            var id = $('#id').val();
            var action = config.admin_url + '/buildings_floors';
            var formData = new FormData($(this)[0]);
            if (id != 0) {
                formData.append('_method', 'PATCH');
                action = config.admin_url + '/buildings_floors/' + id;
            }
            formData.append('building_id', config.building_id);
            $.ajax({
                url: action,
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    $('#addEditBuildingsFloors .submit-form').prop('disabled', false);
                    $('#addEditBuildingsFloors .submit-form').html(lang.save);

                    if (data.type == 'success') {
                        My.toast(data.message);
                        BuildingsFloors_grid.api().ajax.reload();
                        if (id == 0) {
                            BuildingsFloors.empty();
                        } else {
                            $('#addEditBuildingsFloors').modal('hide');
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
                    $('#addEditBuildingsFloors .submit-form').prop('disabled', false);
                    $('#addEditBuildingsFloors .submit-form').html(lang.save);
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
                url: config.admin_url + '/buildings_floors/' + id,
                success: function (data) {
                    console.log(data);

                    BuildingsFloors.empty();
                    My.setModalTitle('#addEditBuildingsFloors', lang.edit);

                    for (i in data.message) {
                        $('#' + i).val(data.message[i]);
                    }
                    $('#addEditBuildingsFloors').modal('show');
                }
            });

        },
        delete: function (t) {

            var id = $(t).attr("data-id");
            My.deleteForm({
                element: t,
                url: config.admin_url + '/buildings_floors/' + id,
                data: {_method: 'DELETE', _token: $('input[name="_token"]').val()},
                success: function (data) {
                    BuildingsFloors_grid.api().ajax.reload();


                }
            });

        },
        delete_lounge: function (t) {

            var id = $(t).attr("data-id");
            My.deleteForm({
                element: t,
                url: config.admin_url + '/buildings_floors/' + id,
                data: {_method: 'DELETE', _token: $('input[name="_token"]').val()},
                success: function (data) {
                    BuildingsFloors_grid.api().ajax.reload();


                }
            });

        },
        add: function () {
            BuildingsFloors.empty();
            My.setModalTitle('#addEditBuildingsFloors', lang.add);
            $('#addEditBuildingsFloors').modal('show');
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
    BuildingsFloors.init();
});