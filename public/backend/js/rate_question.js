var RateQuestions_grid;
var answers_count;
var RateQuestions = function() {

    var init = function() {

        $.extend(lang, new_lang);
        answers_count = $('.answer-one').length;
        //nextLevel = 1;
        handleRecords();
        handleAddOrRemoveItem();
        handleSubmit();


    };

    var handleAddOrRemoveItem = function() {

        $(document).on('click', '.remove-answer', function() {

            var index = $('.answer-one').index($(this).closest('tr'));
            $(this).closest('tr').remove();
            answers_count--;

        });
        $('.add-answer').on('click', function() {
            var langs = JSON.parse(config.languages);
            var inputs;


            var html = '<tr class="answer-one">' +
                '<td>' +
                '<div class="form-group">' +
                '<input placeholder="order" style="width: 100px;" type="number" class="form-control form-filter input-lg"  name="order[' + answers_count + ']" value="">' +
                '<span class="help-block"></span>' +
                '</div>' +
                '</td>';
            for (var x = 0; x < langs.length; x++) {
                html += '<td>' +
                    '<div class="form-group">' +
                    '<input  placeholder="' + langs[x] + '" type="text" class="form-control form-filter input-lg"  name="answers[' + answers_count + '][' + langs[x] + ']" value="">' +
                    '<span class="help-block"></span>' +
                    '</div>' +
                    '</td>';

            }
            html += '<td>' +
                '<a class="btn btn-danger remove-answer">' + lang.remove + '</a></td>' +
                '</tr>';


            $('#answers-table tbody').append(html);
            for (var x = 0; x < langs.length; x++) {
                var ele = "input[name='answers[" + answers_count + "][" + langs[x] + "]']";
                $(ele).rules('add', {
                    required: true
                });
            }

            answers_count++;
        });
    }



    var handleRecords = function() {
        RateQuestions_grid = $('.dataTable').dataTable({
            //"processing": true,
            "serverSide": true,
            "ajax": {
                "url": config.admin_url + "/rate_question/data",
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
        $('#addEditRateQuestionsForm').validate({
            rules: {
                order: {
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
            var ele = "input[name='title[" + langs[x] + "]']";
            $(ele).rules('add', {
                required: true
            });
        }

        // for (var x = 0; x < langs.length; x++) {
        //     var ele = "input[name='answers[" + langs[x] + "]']";
        //     $(ele).rules('add', {
        //         required: true
        //     });
        // }

        $('#addEditRateQuestionsForm .submit-form').click(function() {

            if ($('#addEditRateQuestionsForm').validate().form()) {
                $('#addEditRateQuestionsForm .submit-form').prop('disabled', true);
                $('#addEditRateQuestionsForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function() {
                    $('#addEditRateQuestionsForm').submit();
                }, 1000);
            }
            return false;
        });
        $('#addEditRateQuestionsForm input').keypress(function(e) {
            if (e.which == 13) {
                if ($('#addEditRateQuestionsForm').validate().form()) {
                    $('#addEditRateQuestionsForm .submit-form').prop('disabled', true);
                    $('#addEditRateQuestionsorm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function() {
                        $('#addEditRateQuestionsForm').submit();
                    }, 1000);
                }
                return false;
            }
        });



        $('#addEditRateQuestionsForm').submit(function() {
            var id = $('#id').val();
            var action = config.admin_url + '/rate_question';
            var formData = new FormData($(this)[0]);
            if (id != 0) {
                formData.append('_method', 'PATCH');
                action = config.admin_url + '/rate_question/' + id;
            }
            $.ajax({
                url: action,
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $('#addEditRateQuestionsForm .submit-form').prop('disabled', false);
                    $('#addEditRateQuestionsForm .submit-form').html(lang.save);

                    if (data.type == 'success') {
                        My.toast(data.message);
                        RateQuestions_grid.api().ajax.reload();
                        if (id == 0) {
                            RateQuestions.empty();
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
                    $('#addEditRateQuestionsForm .submit-form').prop('disabled', false);
                    $('#addEditRateQuestionsForm .submit-form').html(lang.save);
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
                url: config.admin_url + '/rate_question/' + id,
                success: function(data) {
                    console.log(data);

                    RateQuestions.empty();
                    My.setModalTitle('#addEditRateQuestionsForm', lang.edit_Common);

                    for (i in data.message) {
                        $('#' + i).val(data.message[i]);
                    }
                    $('#addEditRateQuestionsForm').modal('show');
                }
            });

        },
        delete: function(t) {

            var id = $(t).attr("data-id");
            My.deleteForm({
                element: t,
                url: config.admin_url + '/rate_question/' + id,
                data: { _method: 'DELETE', _token: $('input[name="_token"]').val() },
                success: function(data) {
                    RateQuestions_grid.api().ajax.reload();


                }
            });

        },
        add: function() {
            RateQuestions.empty();
            if (parent_id > 0) {
                $('.for-country').hide();
                $('.for-city').show();
            } else {
                $('.for-country').show();
                $('.for-city').hide();
            }

            My.setModalTitle('#addEditRateQuestionsForm', lang.add_Common);
            $('#addEditRateQuestionsForm').modal('show');
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
    RateQuestions.init();
});