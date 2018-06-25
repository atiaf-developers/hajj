var currentTab = 0;
var suites;
var suite_count;
var previous_selected_suite;
var ids = [];
var BusesAccommodation = function () {

    var init = function () {
        $.extend(lang, new_lang);
        $.extend(config, new_config);
        if (config.action != 'index') {
            showTab(currentTab);
            handle_submit();
            handleShowLounges();
            handleAddOrRemoveItem();
        } else {
            handleCheckAll();
            handleReport();
        }

        Array.prototype.remove = function (v) {
            this.splice(this.indexOf(v) == -1 ? this.length : this.indexOf(v), 1);
        }

    }
    var handleCheckAll = function () {
        $("#check-all-messages").on('change', function () {
            $('.check-one-message').not(this).prop('checked', this.checked);
            enableOrDisableDeleteBtn();
            getCheckedIds();
        });
        $(document).on('change', '.check-one-message', function () {
            if ($(".check-one-message:checked").length == 0) {
                $('#check-all-messages').prop('checked', false);
            }
            enableOrDisableDeleteBtn();
            getCheckedIds();
        });
    }
    var enableOrDisableDeleteBtn = function () {
        if ($(document).find(".check-one-message:checked").length == 0) {
            $(document).find('.btn-delete').prop('disabled', true);
        } else {
            $(document).find('.btn-delete').prop('disabled', false);
        }
    }
    var getCheckedIds = function () {
        var checked_ids = [];
        $(".check-one-message").each(function () {
            if ($(this).is(':checked')) {
                checked_ids.push($(this).data('id'));
            }
        });
        ids = checked_ids;
    }
    var handleReport = function () {
        $('.btn-report').on('click', function () {
            var data = $("#filter-reports").serializeArray();


            var url = config.admin_url + "/buses_accommodation";
            var params = {};
            $.each(data, function (i, field) {
                var name = field.name;
                var value = field.value;
                if (value) {
                    if (name == "from" || name == "to") {
                        value = new Date(Date.parse(value));
                        value = getDate(value);
                    }

                    params[field.name] = field.value
                }

            });
            query = $.param(params);
            url += '?' + query;

            window.location.href = url;
            return false;
        })
    }

    var getDate = function (date) {
        var dd = date.getDate();
        var mm = date.getMonth() + 1; //January is 0!
        var yyyy = date.getFullYear();
        if (dd < 10) {
            dd = '0' + dd
        }
        if (mm < 10) {
            mm = '0' + mm
        }
        var edited_date = yyyy + '-' + mm + '-' + dd;
        return edited_date;
    }
    var handleAddOrRemoveItem = function () {

        $(document).on('click', '.remove-suite', function () {
            $(this).closest('tr').remove();
            var selected = $(this).closest('tr').find('select[name="suite"] option:selected').val();
            //alert(selected);
            $('select[name="suite"] option[value="' + selected + '"]').prop('disabled', false);
            suite_count--;


        });
        $('.add-suite').on('click', function () {
            console.log(getSelectedBuses());
            var html = '<tr class="suite-one">' +
                    '<td style="width:25%;">' +
                    '<div class="form-group form-md-line-input">' +
                    '<select class="form-control" name="suite">' +
                    '<option  value="">' + lang.choose + '</option>';
            //console.log(suites);
            for (var x = 0; x < suites.length; x++) {
                var obj = suites[x];
                var disabled = '';
                var selected_suites = getSelectedBuses();
                if (selected_suites.indexOf(obj.id) != -1) {
                    disabled += 'disabled';
                }
                html += '<option ' + disabled + ' value="' + obj.id + '">' + obj.number + '</option>';
            }



            html += '</select>' +
                    '<label for = "suite">' + lang.suite + '</label>' +
                    '<span class="help-block"></span>' +
                    '</div>' +
                    '</td>' +
                    '<td class="lounges" style="width:70%;">' +
                    '<label>' + lang.lounges + '</label>' +
                    '<div class="box">' +
                    '</div>' +
                    '</td>' +
                    '<td style="width:5%;">' +
                    '<a class="btn btn-danger remove-suite">' + lang.remove + '</a>' +
                    '</td>' +
                    '</tr>';




            $('#lounge-table tbody').append(html);

            suite_count++;
        });
    }
    var handleShowLounges = function () {
        $(document).on('focus', 'select[name="suite"]', function () {
            // Store the current value on focus and on change
            previous_selected_suite = $(this).val();
        }).on('change', 'select[name="suite"]', function () {

            // Do something with the previous value after the change
            if (previous_selected_suite) {
                $('select[name="suite"] option[value="' + previous_selected_suite + '"]').prop('disabled', false);

            }
            // Make sure the previous value is updated
            var ele = $(this);
            var new_selected_suite = ele.val();
            if (new_selected_suite) {
                $('select[name="suite"]').not(this).find('option[value="' + new_selected_suite + '"]').prop('disabled', true);
                getlounges(ele, new_selected_suite);

            } else {
                $(ele).closest('.suite-one').find(".lounges .box").html('');
            }
        });
    }

    var getSelectedBuses = function () {
        var suites = [];
        $('select[name="suite"]').each(function () {
            if ($(this).val()) {
                suites.push(parseInt($(this).val()));
            }
        });
        return suites;
    }


    var getlounges = function (ele, suite) {
        $(ele).closest('.suite-one').find(".lounges .box").html('<p class="text-center"><i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span></p>');
        setTimeout(function () {
            $.get('' + config.admin_url + '/buses_accommodation/lounges/' + suite, function (data) {
                var html = '';
                if (data.data.length != 0)
                {

                    $.each(data.data, function (index, Obj) {
                        html += '<div class="md-checkbox has-success col-md-4">' +
                                '<input type="checkbox" id="' + Obj.id + '-' + Obj.number + '" name="lounges[]" value="' + Obj.id + '" class="md-check">' +
                                '<label for="' + Obj.id + '-' + Obj.number + '">' +
                                '<span class="inc"></span>' +
                                '<span class="check"></span>' +
                                '<span class="box"></span>' + Obj.number + ' ( ' + Obj.available + ' ) ' + '</label>' +
                                '</div>';
                    });

                } else {
                    html += '<p class="text-center">' + lang.no_results + '</p>'
                }
                $(ele).closest('.suite-one').find(".lounges .box").html(html);

            }, "json");
        }, 1000);

    }
    var handle_submit = function () {
        $("#regForm").validate({
            //ignore: "",
            rules: {
//                name: {
//                    required: true
//                },

            },

            highlight: function (element) { // hightlight error inputs
                $(element).closest('.form-group').removeClass('has-success').addClass('has-error');

            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
                $(element).closest('.form-group').find('.help-block').html('');

            },
            errorPlacement: function (error, element) {
                errorElements1.push(element);
                $(element).closest('.form-group').find('.help-block').html($(error).html());
            }

        });

        $('#regForm').submit(function () {
            var formData = new FormData($(this)[0]);
            formData.append('step', currentTab + 1);
            $.ajax({
                url: config.admin_url + "/buses_accommodation",
                type: 'POST',
                dataType: 'json',
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data)
                {
                    console.log(data);


                    $('.alert-danger').hide();
                    $('.alert-success').hide();
                    $('#nextBtn').prop('disabled', false);
                    $('#nextBtn').html(lang.next);
                    if (data.type == 'success') {
                        var step = data.data.step;
                        if (step == 2) {
                            $('.next2').hide();
                            $('.alert-success').show().find('.message').html(data.data.message);
                            created_at=data.data.created_at;
                        } else if (step == 1) {
                            buses = data.data.buses;

                            if (buses.length > 0) {
                                var html = '';

                                for (var x = 0; x < buses.length; x++) {
                                    var Obj = buses[x];
                                    html += '<div class="md-checkbox has-success col-md-4">' +
                                            '<input type="checkbox" id="' + Obj.id + '-' + Obj.number + '" name="buses[]" value="' + Obj.id + '" class="md-check">' +
                                            '<label for="' + Obj.id + '-' + Obj.number + '">' +
                                            '<span class="inc"></span>' +
                                            '<span class="check"></span>' +
                                            '<span class="box"></span>' + Obj.number + ' ( ' + Obj.available + ' ) ' + '</label>' +
                                            '</div>';
                                }

                                $('#buses-box').html(html);
                                $('#pilgrims-count').html(data.data.pilgrims_count);
                            }

                        } else {

                        }
                        var hideTab = currentTab;
                        currentTab = currentTab + 1;
                        $('.tab:eq(' + hideTab + ')').hide();
                        showTab(currentTab);


                    } else {
                        if (typeof data.errors !== 'undefined') {

                            for (i in data.errors)
                            {
                                var message = data.errors[i][0];

                                $('[name="' + i + '"]')
                                        .closest('.form-group').addClass('has-error').removeClass("has-success");
                                $('[name="' + i + '"]').closest('.form-group').find(".help-block").html(message).css('opacity', 1)
                            }



                        }

                        if (typeof data.message !== 'undefined') {
                            $('.tab:eq(' + currentTab + ')').find('.alert-danger').show().find('.message').html(data.message);

                        }
                    }



                },
                error: function (xhr, textStatus, errorThrown) {
                    $('#nextBtn').prop('disabled', false);
                    $('#nextBtn').html(lang.next);
                    My.ajax_error_message(xhr);

                },
            });

            return false;
        });

    }
    var showTab = function (n) {
        // This function will display the specified tab of the form...
        var x = document.getElementsByClassName("tab");
        x[n].style.display = "block";
        //... and fix the Previous/Next buttons:
        if (n == 0) {
            document.getElementById("prevBtn").style.display = "none";
        } else {
            document.getElementById("prevBtn").style.display = "inline";
        }
        if (n == (x.length - 1)) {
            document.getElementById("nextBtn").innerHTML = "احجز";
        } else {
            document.getElementById("nextBtn").innerHTML = "التالى";
        }
        //... and run a function that will display the correct step indicator:
        fixStepIndicator(n)
    }

    var fixStepIndicator = function (n) {
        // This function removes the "active" class of all steps...
        var i, x = document.getElementsByClassName("step");
        for (i = 0; i < x.length; i++) {
            x[i].className = x[i].className.replace(" active", "");
        }
        //... and adds the "active" class on the current step:
        x[n].className += " active";
    }

    return {
        init: function () {
            init();
        },
        empty: function () {
            $('.has-error').removeClass('has-error');
            $('.has-success').removeClass('has-success');
            $('.help-block').html('');

            My.emptyForm();
        },
        nextPrev: function (ele, n) {
            var type = $(ele).data('type');
            var x = document.getElementsByClassName("tab");
            var validate = $('#regForm').validate().form();
            if (type == 'next' && !validate) {
                return false;
            } else {
                if (type == 'next') {
                    $(ele).prop('disabled', true);
                    $(ele).html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
                    setTimeout(function () {
                        $('#regForm').submit();
                    }, 1000);

                    return false;
                } else {
                    var hideTab = currentTab;
                    currentTab = currentTab - 1;
                    $('.tab:eq(' + hideTab + ')').hide();
                    showTab(currentTab);
                }
            }



        },
        delete: function (t) {

            $(t).prop('disabled', true);
            $(t).html('<i class="fa fa-spin fa-spinner"></i>');
            setTimeout(function () {
                $.ajax({
                    url: config.admin_url + '/buses_accommodation/delete',
                    data: {_method: 'DELETE', id: $(t).data('id'), _token: $('input[name="_token"]').val()},
                    success: function (data) {
                        $(t).prop('disabled', false);
                        $(t).html('<i class="fa fa-remove"></i>');
                        console.log(data);
                        if (data.type == 'success') {

                            window.location.reload();

                        } else {

                        }
                    },
                    error: function (xhr, textStatus, errorThrown) {
                        $(t).prop('disabled', false);
                        $(t).html('<i class="fa fa-remove"></i>');
                        My.ajax_error_message(xhr);
                    },
                    dataType: "json",
                    type: "post"
                })
            }, 200);



        },
        notify: function (ele) {
            $(ele).prop('disabled', true);
            $(ele).html('<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>');
            setTimeout(function () {
                var formData = new FormData($('#regForm')[0]);
                formData.append('created_at',created_at)
                $.ajax({
                    url: config.admin_url + "/buses_accommodation/notify",
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    async: false,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (data)
                    {
                        $(ele).prop('disabled', false);
                        $(ele).html(lang.send_notification);


                        if (data.type == 'success') {

                            My.toast(data.message);
                            setTimeout(function () {
                                window.location.href = config.admin_url + "/buses_accommodation";
                            }, 1500);


                        }



                    },
                    error: function (xhr, textStatus, errorThrown) {
                        $(ele).prop('disabled', false);
                        $(ele).html(lang.send_notification);
                        My.ajax_error_message(xhr);

                    },
                });
            }, 1000);



        },
    }

}();

jQuery(document).ready(function () {
    BusesAccommodation.init();
});


