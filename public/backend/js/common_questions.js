var CommonQuestions_grid;

var CommonQuestions = function() {

    var init = function() {

        $.extend(lang, new_lang);
        //nextLevel = 1;
        handleRecords();
        //handleDatatables();
        handleSubmit();


    };



    var handleRecords = function() {
        CommonQuestions_grid = $('.dataTable').dataTable({
            //"processing": true,
            "serverSide": true,
            "ajax": {
                "url": config.admin_url + "/common_question/data",
                "type": "POST",
                data: { _token: $('input[name="_token"]').val() },
            },
            "columns": [
                { "data": "question", "name": "common_questions_translations.question" },
                { "data": "this_order" },
                { "data": "active" },
                { "data": "options", orderable: false, searchable: false }
            ],
            "order": [
                [2, "asc"]
            ],
            "oLanguage": { "sUrl": config.url + '/datatable-lang-' + config.lang_code + '.json' }

        });
    }


    var handleSubmit = function() {
        $('#addEditCommonQuestionsForm').validate({
            rules: {
                'question[]': {
                    required: true,
                },
                'answer[]': {
                    required: true,
                },
                'this_order': {
                    required: true,
                }
            },
            //messages: lang.messages,
            highlight: function(element) { // hightlight error inputs
                $(element).closest('.form-group').removeClass('has-success').addClass('has-error');

            },
            unhighlight: function(element) {
                $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
                $(element).closest('.form-group').find('.help-block').html('').css('opacity', 0);

            },
            errorPlacement: function(error, element) {
                $(element).closest('.form-group').find('.help-block').html($(error).html()).css('opacity', 1);
            }
        });


        var langs = JSON.parse(config.languages);

        for (var x = 0; x < langs.length; x++) {
            var ele = "input[name='question[" + langs[x] + "]']";
            $(ele).rules('add', {
                required: true
            });
        }

        for (var x = 0; x < langs.length; x++) {
            var ele = "textarea[name='answer[" + langs[x] + "]']";
            $(ele).rules('add', {
                required: true
            });
        }

        $('#addEditCommonQuestionsForm .submit-form').click(function() {

            if ($('#addEditCommonQuestionsForm').validate().form()) {
                $('#addEditCommonQuestionsForm .submit-form').prop('disabled', true);
                $('#addEditCommonQuestionsForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function() {
                    $('#addEditCommonQuestionsForm').submit();
                }, 1000);
            }
            return false;
        });
        $('#addEditCommonQuestionsForm input').keypress(function(e) {
            if (e.which == 13) {
                if ($('#addEditCommonQuestionsForm').validate().form()) {
                    $('#addEditCommonQuestionsForm .submit-form').prop('disabled', true);
                    $('#addEditCommonQuestionsorm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function() {
                        $('#addEditCommonQuestionsForm').submit();
                    }, 1000);
                }
                return false;
            }
        });



        $('#addEditCommonQuestionsForm').submit(function() {
            var id = $('#id').val();
            var action = config.admin_url + '/common_question';
            var formData = new FormData($(this)[0]);
            if (id != 0) {
                formData.append('_method', 'PATCH');
                action = config.admin_url + '/common_question/' + id;
            }
            $.ajax({
                url: action,
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $('#addEditCommonQuestionsForm .submit-form').prop('disabled', false);
                    $('#addEditCommonQuestionsForm .submit-form').html(lang.save);

                    if (data.type == 'success') {
                        My.toast(data.message);
                        CommonQuestions_grid.api().ajax.reload();
                        if (id == 0) {
                            CommonQuestions.empty();
                        }


                    } else {
                        if (typeof data.errors !== 'undefined') {
                            console.log(data.errors);
                            for (i in data.errors) {
                                if (i.startsWith('title')) {
                                    var key_arr = i.split('.');
                                    var key_text = key_arr[0] + '[' + key_arr[1] + ']';
                                    i = key_text;
                                }
                                console.log(i);
                                $('[name="' + i + '"]')
                                    .closest('.form-group').addClass('has-error');
                                $('#' + i).parent().find(".help-block").html(data.errors[i]).css('opacity', 1)
                            }
                        }
                    }
                },
                error: function(xhr, textStatus, errorThrown) {
                    $('#addEditCommonQuestionsForm .submit-form').prop('disabled', false);
                    $('#addEditCommonQuestionsForm .submit-form').html(lang.save);
                    My.ajax_error_message(xhr);
                },
                dataType: "json",
                type: "POST"
            });


            return false;

        })




    }

    return {
        init: function() {
            init();
        },
        edit: function(t) {
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
                url: config.admin_url + '/common_question/' + id,
                success: function(data) {
                    console.log(data);

                    CommonQuestions.empty();
                    My.setModalTitle('#addEditCommonQuestionsForm', lang.edit_Common);

                    for (i in data.message) {
                        $('#' + i).val(data.message[i]);
                    }
                    $('#addEditCommonQuestionsForm').modal('show');
                }
            });

        },
        delete: function(t) {

            var id = $(t).attr("data-id");
            My.deleteForm({
                element: t,
                url: config.admin_url + '/common_question/' + id,
                data: { _method: 'DELETE', _token: $('input[name="_token"]').val() },
                success: function(data) {
                    CommonQuestions_grid.api().ajax.reload();


                }
            });

        },
        add: function() {
            CommonQuestions.empty();
            if (parent_id > 0) {
                $('.for-country').hide();
                $('.for-city').show();
            } else {
                $('.for-country').show();
                $('.for-city').hide();
            }

            My.setModalTitle('#addEditCommonQuestionsForm', lang.add_Common);
            $('#addEditCommonQuestionsForm').modal('show');
        },

        error_message: function(message) {
            $.alert({
                title: lang.error,
                content: message,
                type: 'red',
                typeAnimated: true,
                buttons: {
                    tryAgain: {
                        text: lang.try_again,
                        btnClass: 'btn-red',
                        action: function() {}
                    }
                }
            });
        },
        empty: function() {
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
jQuery(document).ready(function() {
    CommonQuestions.init();
});