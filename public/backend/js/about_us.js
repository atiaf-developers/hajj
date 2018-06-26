var AboutUs = function () {

    var init = function () {
        handleSubmit();
        handleAboutUsVideo();
        //handleChangeDeclrativeYoutubeUrl();
    
        My.readImageMulti('mena_supervisor_image');
        My.readImageMulti('muzdalifah_supervisor_image');
        My.readImageMulti('arafat_supervisor_image');
    };

  


    var handleAboutUsVideo = function () {
        $('#about_youtube_url_input').on('change', function () {
            var value = $(this).val();
            if (value && value != '') {
                var myId = My.getYoutubeEmbedUrl(value);

                $('#about_youtube_url').val(myId);

                $('#about-youtube-iframe').html('<iframe width="100%" height="315" src="//www.youtube.com/embed/' + myId + '" frameborder="0" allowfullscreen></iframe>');
            }

        })
        $('#declarative_youtube_url_input').on('change', function () {
            var value = $(this).val();
           
            if (value && value != '') {
                var myId = My.getYoutubeEmbedUrl(value);

                $('#declarative_youtube_url').val(myId);

                $('#declarative-youtube-iframe').html('<iframe width="100%" height="315" src="//www.youtube.com/embed/' + myId + '" frameborder="0" allowfullscreen></iframe>');
            }

        })

    }

   

    var handleSubmit = function () {

        $('#editAboutUsForm').validate({
            ignore: "",
            rules: {
                'setting[about_type]': {
                    required: true
                },
                'setting[youtube_url_input]': {
                    //required: true
                },
                'about_video_url': {
                    accept: "mp4",
                    filesize: 1000 * 25000
                },
                //                'setting[video_type]':{
//                    required: true
//                },
//                'setting[phone]': {
//                    required: true
//                },
//                'setting[phone_2]': {
//                    required: true
//                },
//                'setting[email]': {
//                    required: true,
//                    email: true
//                },



            },
            messages: lang.messages,
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
            var about = "textarea[name='setting[info][about_text][" + langs[x] + "]']";

            $(about).rules('add', {
                required: true
            });
        }
        $('#editAboutUsForm .submit-form').click(function () {
            if ($('#editAboutUsForm').validate().form()) {
                $('#editAboutUsForm .submit-form').prop('disabled', true);
                $('#editAboutUsForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                setTimeout(function () {
                    $('#editAboutUsForm').submit();
                }, 500);
            }
            return false;
        });
        $('#editAboutUsForm input').keypress(function (e) {
            if (e.which == 13) {
                if ($('#editAboutUsForm').validate().form()) {
                    $('#editAboutUsForm .submit-form').prop('disabled', true);
                    $('#editAboutUsForm .submit-form').html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#editAboutUsForm').submit();
                    }, 500);
                }
                return false;
            }
        });



        $('#editAboutUsForm').submit(function () {
            var id = $('#id').val();
            var action = config.admin_url + '/about_us';
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: action,
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    $('#editAboutUsForm .submit-form').prop('disabled', false);
                    $('#editAboutUsForm .submit-form').html(lang.save);

                    if (data.type == 'success') {

                        toastr.options = {
                            "debug": false,
                            "positionClass": "toast-bottom-left",
                            "onclick": null,
                            "fadeIn": 300,
                            "fadeOut": 1000,
                            "timeOut": 5000,
                            "extendedTimeOut": 1000
                        };
                        toastr.success(lang.updated_successfully, 'رسالة');

                    } else {
                        console.log(data)
                        if (typeof data.errors === 'object') {
                            for (i in data.errors) {
                                var message = data.errors[i];
                                var key_arr = i.split('.');
                                var name = '';
                                for (var x = 0; x < key_arr.length; x++) {
                                    if (x == 0) {
                                        name += key_arr[x];
                                    } else {
                                        name += '[' + key_arr[x] + ']';
                                    }
                                }
                                i = name;
                                console.log(i);

                                $('[name="' + i + '"]')
                                        .closest('.form-group').addClass('has-error');
                                $('[name="' + i + '"]').closest('.form-group').find(".help-block").html(message).css('opacity', 1);
                            }
                        }
                        if (typeof data.message !== 'undefined') {
                            $.confirm({
                                title: lang.error,
                                content: data.message,
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
                        }
                    }
                },
                error: function (xhr, textStatus, errorThrown) {
                    $('#editAboutUsForm .submit-form').prop('disabled', false);
                    $('#editAboutUsForm .submit-form').html(lang.save);
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
        }
    };

}();
jQuery(document).ready(function () {
    AboutUs.init();
});