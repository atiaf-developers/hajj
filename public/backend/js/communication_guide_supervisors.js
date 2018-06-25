var CommunicationGuideSupervisors_grid;
var communication_guide;

var CommunicationGuideSupervisors = function () {
    var init = function () {
        $.extend(lang, new_lang);
        communication_guide = lang.communication_guide;
        handleRecords();
        handleSubmit();
        My.readImageMulti('image');
        

    };

    var handleRecords = function () {
        CommunicationGuideSupervisors_grid = $('.dataTable').dataTable({
            //"processing": true,
            "serverSide": true,
            "ajax": {
                "url": config.admin_url + "/communication_guides_supervisors/data",
                "type": "POST",
                data: {communication_guide: communication_guide,_token: $('input[name="_token"]').val()},
            },
            "columns": [
//                    {"data": "user_input", orderable: false, "class": "text-center"},
                {"data": "name","name":"supervisors.name"},
                {"data": "supervisor_image", orderable: false, searchable: false},
                {"data": "options", orderable: false, searchable: false}
            ],
            
            "oLanguage": {"sUrl": config.url + '/datatable-lang-' + config.lang_code + '.json'}

        });
    }
    var handleSubmit = function () {

        $('#addEditCommunicationGuideSupervisorsForm').validate({
            rules: {
                name: {
                    required: true
                },
                contact_numbers: {
                    required: true,
                },
                job: {
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
        $('#addEditCommunicationGuideSupervisors .submit-form').click(function () {
            if ($('#addEditCommunicationGuideSupervisorsForm').validate().form()) {
                $('#addEditCommunicationGuideSupervisors .submit-form').prop('disabled', true);
                $('#addEditCommunicationGuideSupervisors .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#addEditCommunicationGuideSupervisorsForm').submit();
                }, 1000);

            }
            return false;
        });
        $('#addEditCommunicationGuideSupervisorsForm input').keypress(function (e) {
            if (e.which == 13) {
                if ($('#addEditCommunicationGuideSupervisorsForm').validate().form()) {
                    $('#addEditCommunicationGuideSupervisors .submit-form').prop('disabled', true);
                    $('#addEditCommunicationGuideSupervisors .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#addEditCommunicationGuideSupervisorsForm').submit();
                    }, 1000);
                }
                return false;
            }
        });



        $('#addEditCommunicationGuideSupervisorsForm').submit(function () {
            var id = $('#id').val();
            var formData = new FormData($(this)[0]);
            formData.append('communication_guide',communication_guide);
            var action = config.admin_url + '/communication_guides_supervisors';
            if (id != 0) {
                formData.append('_method', 'PATCH');
                action = config.admin_url + '/communication_guides_supervisors/' + id;
            }


            $.ajax({
                url: action,
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    $('#addEditCommunicationGuideSupervisors .submit-form').prop('disabled', false);
                    $('#addEditCommunicationGuideSupervisors .submit-form').html(lang.save);

                    if (data.type == 'success')
                    {
                        My.toast(data.message);
                        CommunicationGuideSupervisors_grid.api().ajax.reload();

                        if (id != 0) {
                            $('#addEditCommunicationGuideSupervisors').modal('hide');
                        } else {
                            CommunicationGuideSupervisors.empty();
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
                    $('#addEditCommunicationGuideSupervisors .submit-form').prop('disabled', false);
                    $('#addEditCommunicationGuideSupervisors .submit-form').html(lang.save);
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
                url: config.admin_url + '/communication_guides_supervisors/' + id,
                success: function (data)
                {
                    console.log(data);

                    CommunicationGuideSupervisors.empty();
                    My.setModalTitle('#addEditCommunicationGuideSupervisors', lang.edit);

                    for (i in data.message)
                    {
                        if (i == 'supervisor_image') {
                            $('.image').prop('src', config.url+'/public/uploads/supervisors/'+data.message[i]);
                        }

                        if (i == 'supervisor_job_id') {
                            $('#job').val(data.message[i]);
                        }

                        $('#' + i).val(data.message[i]);
                    }
                    $('#addEditCommunicationGuideSupervisors').modal('show');
                }
            });

        },
        delete: function (t) {
            var id = $(t).attr("data-id");
            My.deleteForm({
                element: t,
                url: config.admin_url + '/communication_guides_supervisors/' + id,
                data: {_method: 'DELETE', _token: $('input[name="_token"]').val()},
                success: function (data)
                {

                    CommunicationGuideSupervisors_grid.api().ajax.reload();


                }
            });
        },
        add: function () {
            CommunicationGuideSupervisors.empty();
            My.setModalTitle('#addEditCommunicationGuideSupervisors', lang.add);
            $('#addEditCommunicationGuideSupervisors').modal('show');
        },
        empty: function () {
            $('#id').val(0);
            $('#active').find('option').eq(0).prop('selected', true);
            $('.has-error').removeClass('has-error');
            $('.has-success').removeClass('has-success');
            $('.help-block').html('');
            $('.image_box').html('<img src="' + config.url + '/no-image.png" class="image" width="150" height="80" />');

            My.emptyForm();
        },
    };
}();
$(document).ready(function () {
    CommunicationGuideSupervisors.init();
});