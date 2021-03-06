var currentTab = 0;
var suites;
var buildings;
var building_count = 1;
var previous_selected_floor;
var ids = [];
var created_at;
var BuildingsAccommodation = function () {

    var init = function () {
        $.extend(lang, new_lang);
        $.extend(config, new_config);
        if (typeof config.buildings !== 'undefined') {
            buildings = JSON.parse(config.buildings);
            console.log(buildings);
        }
        if (config.action != 'index') {
            showTab(currentTab);
            handle_submit();
            handleShowLounges();
            handleAddOrRemoveItem();
            handleChangeBuilding();
        } else {
            handleReport();
            handleCheckAll();
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


            var url = config.admin_url + "/buildings_accommodation";
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
            var selected = $(this).closest('tr').find('select[name="floor"] option:selected').val();
            //alert(selected);
            $('select[name="floor"] option[value="' + selected + '"]').prop('disabled', false);
            building_count--;


        });
        $('.add-suite').on('click', function () {
            building_count++;
            var html = '<tr class="room-one">' +
                    '<td style="width:40%;">' +
                    '<div class="form-group form-md-line-input col-md-6">' +
                    '<select class="form-control" name="building">' +
                    '<option  value="">' + lang.choose + '</option>';
            //console.log(suites);
            for (var x = 0; x < buildings.length; x++) {
                var obj = buildings[x];

                html += '<option  value="' + obj.id + '">' + obj.number + '</option>';
            }

            html += '</select>' +
                    '<label for = "building">' + lang.building + '</label>' +
                    '<span class="help-block"></span>' +
                    '</div>' +
                    '<div class="form-group form-md-line-input col-md-6">' +
                    '<select class="form-control" id="floor' + building_count + '" name="floor">' +
                    '<option  value="">' + lang.choose + '</option>';




            html += '</select>' +
                    '<label for = "floor">' + lang.floor + '</label>' +
                    '<span class="help-block"></span>' +
                    '</div>' +
                    '</td>' +
                    '<td class="rooms" style="width:55%;">' +
                    '<label>' + lang.rooms + '</label>' +
                    '<div class="box">' +
                    '</div>' +
                    '</td>' +
                    '<td style="width:5%;">' +
                    '<a class="btn btn-danger remove-suite">' + lang.remove + '</a>' +
                    '</td>' +
                    '</tr>';




            $('#rooms-table tbody').append(html);


        });
    }
    var handleShowLounges = function () {
        $(document).on('focus', 'select[name="floor"]', function () {
            // Store the current value on focus and on change
            previous_selected_floor = $(this).val();
        }).on('change', 'select[name="floor"]', function () {

            // Do something with the previous value after the change
            if (previous_selected_floor) {
                $('select[name="floor"] option[value="' + previous_selected_floor + '"]').prop('disabled', false);

            }
            // Make sure the previous value is updated
            var ele = $(this);
            var new_selected_suite = ele.val();
            if (new_selected_suite) {
                $('select[name="floor"]').not(this).find('option[value="' + new_selected_suite + '"]').prop('disabled', true);
                getRooms(ele, new_selected_suite);

            } else {
                $(ele).closest('.room-one').find(".rooms .box").html('');
            }
        });
    }

    var getSelectedFloors = function () {
        var suites = [];
        $('select[name="floor"]').each(function () {
            if ($(this).val()) {
                suites.push(parseInt($(this).val()));
            }
        });
        return suites;
    }

    var handleChangeBuilding = function (ele, suite) {
        $(document).on('change', 'select[name="building"]', function () {
            var building = $(this).val();
            var gender = $('#gender').val();
            var index = $('select[name^="building"]').index($(this));
            var html = '<option value="">' + lang.choose + '</option>';
            if (building && building != '') {
                $.get('' + config.admin_url + '/buildings_accommodation/floors?building=' + building + '&gender=' + gender, function (data) {
                    if (data.data.length != 0)
                    {
                        $.each(data.data, function (index, Obj) {
                            var disabled = '';
                            var selected_floors = getSelectedFloors();
                            if (selected_floors.indexOf(Obj.id) != -1) {
                                disabled += 'disabled';
                            }
                            html += '<option ' + disabled + ' value="' + Obj.id + '">' + Obj.number + '</option>';
                        });
                    }
                    console.log(data.data);
                    $('select[name="floor"]').eq(index).html(html);

                }, "json");
            } else {
                $('select[name="floor"]').eq(index).html(html);
            }
        });


    }
    var getRooms = function (ele, suite) {
        $(ele).closest('.room-one').find(".rooms .box").html('<p class="text-center"><i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span></p>');
        setTimeout(function () {
            $.get('' + config.admin_url + '/buildings_accommodation/rooms/' + suite, function (data) {
                var html = '';
                if (data.data.length != 0)
                {

                    $.each(data.data, function (index, Obj) {
                        html += '<div class="md-checkbox has-success col-md-4">' +
                                '<input type="checkbox" id="' + Obj.id + '-' + Obj.number + '" name="rooms[]" value="' + Obj.id + '" class="md-check">' +
                                '<label for="' + Obj.id + '-' + Obj.number + '">' +
                                '<span class="inc"></span>' +
                                '<span class="check"></span>' +
                                '<span class="box"></span>' + Obj.number + ' ( ' + Obj.available + ' ) ' + '</label>' +
                                '</div>';
                    });

                } else {
                    html += '<p class="text-center">' + lang.no_results + '</p>'
                }
                $(ele).closest('.room-one').find(".rooms .box").html(html);

            }, "json");
        }, 1000);

    }
    var handle_submit = function () {
        if ($("#regForm").length > 0) {
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
                    url: config.admin_url + "/buildings_accommodation",
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
                                created_at=data.data.created_at
                            } else if (step == 1) {
                                $('.rooms .box').html('');
                                $(document).find('select[name="building"]').find('option').eq(0).prop('selected', true);
                                $(document).find('select[name="floor"]').html('<option value="">' + lang.choose + '</option>');
                                $('#pilgrims-count').html(data.data.pilgrims_count);

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
                    url: config.admin_url + '/buildings_accommodation/delete',
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
                $.ajax({
                    url: config.admin_url + "/buildings_accommodation/notify",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        _token: $('input[name="_token"]').val(),
                        created_at: created_at
                    },
                    async: false,
                    success: function (data)
                    {
                        $(ele).prop('disabled', false);
                        $(ele).html(lang.send_notification);


                        if (data.type == 'success') {

                            My.toast(data.message);
                            setTimeout(function () {
                                window.location.href = config.admin_url + "/buildings_accommodation";
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
    BuildingsAccommodation.init();
});


