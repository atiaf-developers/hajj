var CommunicationGuides_grid;

var CommunicationGuides = function () {

    var init = function () {
        $.extend(lang, new_lang);
        $.extend(config, new_config);
        handleRecords();
        handleSubmit();
        My.readImageMulti('image');
    };

    var handleRecords = function () {
        CommunicationGuides_grid = $('.dataTable').dataTable({
            //"processing": true,
            "serverSide": true,
            "ajax": {
                "url": config.admin_url + "/communication_guides/data",
                "type": "POST",
                data: {_token: $('input[name="_token"]').val()},
            },
            "columns": [
                {"data": "title","name":"communication_guides_translations.title"},
                {"data": "this_order","name":"communication_guides.this_order"},
                {"data": "active","name":"communication_guides.this_order"},
                {"data": "options", orderable: false, searchable: false}
            ],
            "order": [
                [2, "asc"]
            ],
            "oLanguage": {"sUrl": config.url + '/datatable-lang-' + config.lang_code + '.json'}

        });
    }


    var handleSubmit = function () {
        $('#addEditCommunicationGuidesForm').validate({
            rules: {
                
                 active: {
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
        
        
       var langs = JSON.parse(config.languages);
        for (var x = 0; x < langs.length; x++) {

             var ele = "input[name='title[" + langs[x] + "]']";
             var ele2 = "input[name='description[" + langs[x] + "]']";
             $(ele).rules('add', {
                 required: true
             });
             $(ele2).rules('add', {
                 required: true
             });
         }

        $('#addEditCommunicationGuidesForm .submit-form').click(function () {

            if ($('#addEditCommunicationGuidesForm').validate().form()) {
                $('#addEditCommunicationGuidesForm .submit-form').prop('disabled', true);
                $('#addEditCommunicationGuidesForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#addEditCommunicationGuidesForm').submit();
                }, 1000);
            }
            return false;
        });
        $('#addEditCommunicationGuidesForm input').keypress(function (e) {
            if (e.which == 13) {
                if ($('#addEditCommunicationGuidesForm').validate().form()) {
                    $('#addEditCommunicationGuidesForm .submit-form').prop('disabled', true);
                    $('#addEditCommunicationGuidesForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#addEditCommunicationGuidesForm').submit();
                    }, 1000);
                }
                return false;
            }
        });



        $('#addEditCommunicationGuidesForm').submit(function () {
            var id = $('#id').val();
            var action = config.admin_url + '/communication_guides';
            var formData = new FormData($(this)[0]);
            
            if (id != 0) {
                formData.append('_method', 'PATCH');
                action = config.admin_url + '/communication_guides/' + id;
            }
            $.ajax({
                url: action,
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    $('#addEditCommunicationGuidesForm .submit-form').prop('disabled', false);
                    $('#addEditCommunicationGuidesForm .submit-form').html(lang.save);

                    if (data.type == 'success')
                    {
                        My.toast(data.message);
                        if (id == 0) {
                            CommunicationGuides.empty();
                        }


                    } else {
                        if (typeof data.errors !== 'undefined') {
                            for (i in data.errors)
                            {
                                var message=data.errors[i];
                                 if (i.startsWith('title') || i.startsWith('description')) {
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
                    $('#addEditCommunicationGuidesForm .submit-form').prop('disabled', false);
                    $('#addEditCommunicationGuidesForm .submit-form').html(lang.save);
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

        delete: function (t) {

            var id = $(t).attr("data-id");
            My.deleteForm({
                element: t,
                url: config.admin_url + '/communication_guides/' + id,
                data: {_method: 'DELETE', _token: $('input[name="_token"]').val()},
                success: function (data)
                {
                    CommunicationGuides_grid.api().ajax.reload();


                }
            });

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
            $('#category_icon').val('');
            $('#active').find('option').eq(0).prop('selected', true);
            $('input[type="checkbox"]').prop('checked', false);
            $('.image_box').html('<img src="' + config.url + '/no-image.png" class="image" width="150" height="80" />');
            $('.has-error').removeClass('has-error');
            $('.has-success').removeClass('has-success');
            $('.help-block').html('');
             $('#contact-numbers-table tbody').html('');
            My.emptyForm();
        }
    };

}();
jQuery(document).ready(function () {
    CommunicationGuides.init();
});

