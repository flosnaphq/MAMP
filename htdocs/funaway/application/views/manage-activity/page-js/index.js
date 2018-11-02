
//"use strict";

$(document).data("changed", false);

var confirmBox = function (succescallBack) {

    $('.confirm').modaal({
        type: 'confirm',
        confirm_button_text: 'Continue',
        confirm_cancel_button_text: 'Cancel',
        confirm_title: 'Warning',
        confirm_content: '<p>Please Save Unsave Content</p>',
        confirm_callback: succescallBack,
    });
};
var activityApp = angular.module('activityApp', ["ngRoute"]);
activityApp.directive('autoActive', ['$location', function ($location) {
        return {
            restrict: 'A',
            scope: false,
            link: function (scope, element) {
                function setActive() {
                    var completedSvg = '<svg class="icon icon--check">';
                    completedSvg += '<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-check"></use></svg>';
                    var path = $location.path();
                    var activeMet = false;
                    var counter = 1;
                    if (path) {
                        angular.forEach(element.find('li'), function (li) {
                            var anchor = li.querySelector('a');

                            if (anchor.href.match('#!' + path + '(?=\\?|$)')) {
                                angular.element(li).find('a').addClass('active');
                                activeMet = true;

                            } else {
                                angular.element(li).find('a').removeClass('active');
                            }

                            if (!activeMet) {
                                angular.element(li).find('a').addClass('done');
                                angular.element(li).find('span').html(completedSvg);
                            } else {
                                angular.element(li).find('a').removeClass('done');
                                angular.element(li).find('span').html("<strong>" + counter + "</strong>");
                            }
                            counter++;
                        });
                    }
                }
                setActive();
                scope.$on('$locationChangeSuccess', setActive);
            }
        };
    }]);

activityApp.controller("MainController", function ($scope, $rootScope, $location, $route, $window, $templateCache) {

    $rootScope.reloadMe = function () {
        $(document).data("changed", false);
        var currentPageTemplate = $route.current.templateUrl;
        $templateCache.remove(currentPageTemplate);
        $route.reload();
    };


    $rootScope.nextPage = function (step) {
        switch (step) {
            case 1:
                $location.path("/activity-brief");
                break;
            case 2:
                $location.path("/availablity");
                break;
            case 3:
                $location.path("/photos");
                break;
            case 4:
                $location.path("/videos");
                break;
            case 5:
                $location.path("/map");
                break;
            case 6:
                $location.path("/addons");
                break;
        }
        $window.scrollTo(0, 0);
    };
    $rootScope.prevPage = function (step) {
        switch (step) {
            case 2:
                $location.path("/");
                break;
            case 3:
                $location.path('/activity-brief');
                break;
            case 4:
                $location.path("/availablity");
                break;
            case 5:
                $location.path("/photos");
                break;
            case 6:
                $location.path("/videos");
                break;
            case 7:
                $location.path("/map");
                break;

        }
        $window.scrollTo(0, 0);
    };
});

activityApp.controller("MapController", ['$scope', function ($scope) {

        $scope.saveMapInfo = function ($event) {
            jsonStrictNotifyMessage();
            $('#frmMap').ajaxSubmit({
                delegation: true,
                success: function (json) {
                    json = $.parseJSON(json);
                    if (json.status == "1") {
                        jsonSuccessMessage(json.msg);
                        $('#verify_change').val("");
                    } else {
                        jsonErrorMessage(json.msg);
                    }
                }
            });
            $event.preventDefault();
            return false;
        }

    }]);


activityApp.controller("VideosController", ['$scope', function ($scope) {
        jQuery.fn.formModfied('frmVideo');

        $scope.saveVideo = function ($event) {
            var v = setup3Validator;
            $('#frmVideo').ajaxSubmit({
                delegation: true,
                beforeSubmit: function () {
                    v.validate();
                    if (!v.isValid()) {
                        return false;
                    }
                    jsonStrictNotifyMessage();
                },
                success: function (json) {

                    json = $.parseJSON(json);
                    if (json.status == "1") {
                        jsonSuccessMessage(json.msg);
                        $scope.reloadMe();
                    } else {
                        jsonErrorMessage(json.msg);
                    }
                }
            });

            $event.preventDefault();
            return false;
        };

        $scope.removeVideo = function (fileId) {
            if (confirm('Are You Sure')) {
                jsonStrictNotifyMessage();
                $.ajax({
                    url: fcom.makeUrl('hostactivity', 'remove-video'),
                    type: 'post',
                    data: {'file_id': fileId},
                    success: function (json) {

                        json = $.parseJSON(json);
                        if (json.status == 1) {
                            jsonSuccessMessage(json.msg);
                            $scope.reloadMe();
                        } else {
                            jsonErrorMessage(json.msg);

                        }
                    }
                });
            }
        };

    }]);
activityApp.controller("EventsController", ['$scope', '$window', function ($scope, $window) {

        var initialize = function () {

            $('.modaal-ajax').modaal({
                type: 'ajax'
            });
            $('.available-instruction-popup').modaal();
            $('.prior-instruction-popup').modaal();
            $('.bulk-entry-instruction-popup').modaal();

        }

        initialize();


    }]);
activityApp.controller("BriefController", ['$scope', '$window', '$rootScope', function ($scope, $window, $rootScope) {

        jQuery.fn.formModfied('step4');
        $scope.saveActivityBrief = function ($event) {
            var v = $window.step4Validator;
            $('#step4').ajaxSubmit({
                delegation: true,
                beforeSubmit: function () {
                    v.validate();
                    if (!v.isValid()) {
                        return false;
                    }
                    jsonStrictNotifyMessage();
                },
                success: function (json) {

                    json = $.parseJSON(json);
                    if (json.status == "1") {
                        jsonSuccessMessage(json.msg);
                        if ($rootScope.progress < 2)
                            $rootScope.progress = 2;
                        $(document).data("changed", false);

                    } else {
                        jsonErrorMessage(json.msg);

                    }
                }

            });
            $event.preventDefault();
            return false;
        }




        var initialize = function () {

            $('.meeting-points').modaal();
            $('.cancellation-popup').modaal({
                width: 20
            });
        }

        initialize();


    }]);

activityApp.controller("AddonController", ['$scope', '$window', '$compile', function ($scope, $window, $compile) {

        jQuery.fn.formModfied('frmAddons');

        var bindAddonImageUploader = function (addon_id) {
            $('.addon-modaal-ajax').modaal({
                type: 'ajax',
                after_open: function () {
                    var settings = {
                        'aspectRatio': 16 / 9,
                        'url': fcom.makeUrl('croper', 'activityAddonImage', [addon_id]),
                        'afterSaveCallback': function () {

                            $('.addon-modaal-ajax').modaal('close');
                            $scope.addonImages(addon_id);

                        }
                    }
                    var croper = new CropAvatar(settings);
                }
            });

        };


        $scope.addonImages = function (addon_id) {


            jsonNotifyMessage('Loading...');
            $.ajax({
                url: fcom.makeUrl('manage-activity', 'addonImages'),
                data: {addon_id: addon_id},
                type: 'post',
                success: function (json) {

                    json = $.parseJSON(json);
                    if (json.status == 1) {
                        jsonRemoveMessage();
                        $('.form-section').html(json.msg);
                        bindAddonImageUploader(addon_id);
                        $compile($('.form-section').contents())($scope);
                    } else {
                        jsonErrorMessage(json.msg);
                    }
                }
            });
        }

        $scope.uploadAddonImage = function (v) {

            $('#addonImageFrm').ajaxSubmit({
                delegation: true,
                beforeSubmit: function () {
                    v.validate();
                    if (!v.isValid()) {
                        return false;
                    }
                    jsonStrictNotifyMessage();
                },
                success: function (json) {

                    json = $.parseJSON(json);
                    if (json.status == "1") {
                        $(document).data("changed", false);
                        jsonSuccessMessage(json.msg);
                        $scope.addonImages(json.addon_id);
                    } else {
                        jsonErrorMessage(json.msg);


                    }
                }
            });
        }
        $scope.removeAddonImage = function (image_id) {
            if (confirm('Do You want to delete?')) {
                jsonStrictNotifyMessage();
                fcom.ajax(fcom.makeUrl('hostactivity', 'removeAddonImage'), {image_id: image_id}, function (json) {
                    json = $.parseJSON(json);
                    if (json.status == 1) {
                        jsonSuccessMessage(json.msg);
                        $scope.addonImages(json.addon_id);
                    } else {
                        jsonErrorMessage(json.msg);
                    }
                });
            }


        }


        $scope.saveAddon = function ($event) {
            var v = $window.setup7Validator;
            $('#frmAddons').ajaxSubmit({
                delegation: true,
                beforeSubmit: function () {
                    v.validate();
                    if (!v.isValid()) {
                        return false;
                    }
                    jsonStrictNotifyMessage();
                },
                success: function (json) {
                    json = $.parseJSON(json);
                    if (json.status == "1") {
                        jsonSuccessMessage(json.msg);
                        $scope.reloadMe();
                    } else {
                        jsonErrorMessage(json.msg);
                    }
                }
            });
            $event.preventDefault();
            return false;
        }


        $scope.editAddon = function (addon_id) {
            if (typeof addon_id == 'undefined' || addon_id == 'null') {
                return false;
            }
            jsonStrictNotifyMessage();
            fcom.ajax(fcom.makeUrl('manage-activity', 'editAddon'), {addon_id: addon_id}, function (json) {
                json = $.parseJSON(json);
                if (json.status == 1) {
                    $('.form-section').html(json.msg);
                    $compile($('.form-section').contents())($scope);

                    jsonRemoveMessage();
                } else {
                    jsonErrorMessage(json.msg);
                }
            });

        }

        $scope.removeAddon = function (fileId) {
            if (confirm('Are You Sure')) {
                jsonStrictNotifyMessage();
                $.ajax({
                    url: fcom.makeUrl('hostactivity', 'remove-addons'),
                    type: 'post',
                    data: {'addon_id': fileId},
                    success: function (json) {

                        json = $.parseJSON(json);
                        if (json.status == 1) {
                            jsonSuccessMessage(json.msg);

                            $scope.reloadMe();
                        } else
                            jsonErrorMessage(json.msg);
                    }
                });
            }
        }

    }]);

activityApp.controller("ImageController", ['$scope', function ($scope) {

        var bindImageUploader = function () {
            $('.modaal-ajax').modaal({
                type: 'ajax',
                after_open: function () {
                    var settings = {
                        'aspectRatio': 16 / 9,
                        'url': fcom.makeUrl('croper', 'activityImage'),
                        'afterSaveCallback': function () {

                            $('.modaal-ajax').modaal('close');

                            $scope.reloadMe();
                        }
                    }
                    var croper = new CropAvatar(settings);
                }
            });

        };
        bindImageUploader();



        $scope.removeImage = function (fileId) {
            if (confirm('Are You Sure')) {
                jsonStrictNotifyMessage();
                $.ajax({
                    url: fcom.makeUrl('manage-activity', 'remove-image'),
                    type: 'post',
                    data: {'file_id': fileId},
                    success: function (json) {

                        json = $.parseJSON(json);
                        if (json.status == 1) {
                            jsonSuccessMessage(json.msg);
                            $(document).data("changed", false);
                            $scope.reloadMe();
                        } else {
                            jsonErrorMessage(json.msg);

                        }
                    }
                });
            }
        }
        $scope.setDefault = function (image_id) {
            jsonStrictNotifyMessage();
            $.ajax({
                url: fcom.makeUrl('manage-activity', 'default-image'),
                type: 'post',
                data: {'image_id': image_id},
                success: function (json) {

                    json = $.parseJSON(json);
                    if (json.status == "1") {
                        jsonSuccessMessage(json.msg);
                        $scope.reloadMe();
                    } else {
                        jsonErrorMessage(json.msg);


                    }
                }
            });
        }

    }]);

activityApp.controller("BasicController", ['$scope', '$window', '$rootScope', function ($scope, $window, $rootScope) {

        var initialize = function () {

            $('.request-team').modaal();
            $('.js-status-info').modaal();
            $('.js-booking-status-info').modaal();
            $('.js-activity-display-price').modaal();
            $('.date-popup').modaal({
                width: 20
            });
            $('.js-commission-popup').modaal({
                width: 20
            });
            $('.modaal-ajax').modaal({
                type: 'ajax'
            });
            jQuery.fn.formModfied('step1');
            changeBooking('#booking-day');
            changeDuration('#duration-day');
        };

        $scope.setupBasicInfo = function ($event) {

            v = $window.step1Validator;
            angular.element('#step1').ajaxSubmit({
                delegation: true,
                beforeSubmit: function () {
                    v.validate();
                    if (!v.isValid()) {
                        return false;
                    }
                    jsonStrictNotifyMessage();
                },
                success: function (json) {

                    json = $.parseJSON(json);
                    if (json.status == "1") {
                        angular.element(document).data("changed", false);
                        if ($rootScope.progress < 1)
                            $rootScope.progress = 1;
                        jsonSuccessMessage(json.msg);
                        $('#js-basicToNext').css({'display': 'block'})
                    } else {
                        jsonErrorMessage(json.msg);
                    }
                }
            });
            $event.preventDefault();
            return false;
        }
        initialize();
    }]);


activityApp.config(function ($routeProvider) {
    $routeProvider
            .when("/", {
                templateUrl: "manage-activity/basic",
                controller: 'BasicController',
            })
            .when("/photos", {
                templateUrl: "manage-activity/photos",
                controller: 'ImageController',
            })
            .when("/videos", {
                templateUrl: "manage-activity/videos",
                controller: 'VideosController',
            })
            .when("/map", {
                templateUrl: "manage-activity/map",
                controller: 'MapController',
            })
            .when("/availablity", {
                templateUrl: "manage-activity/availablity",
                controller: 'EventsController',
            })
            .when("/addons", {
                templateUrl: "manage-activity/addon",
                controller: "AddonController",
            })
            .when("/activity-brief", {
                templateUrl: "manage-activity/activity-brief",
                controller: 'BriefController',
            });
}).run(function ($rootScope, $location, $route, $window, $templateCache) {

    $rootScope.$on('$viewContentLoaded', function () {
        $templateCache.removeAll();
    });
    $rootScope.progress = $window.activityState;
    $rootScope.$on("$routeChangeStart", function (event, next, current) {

        //jsonNotifyMessage('Loading...');	

        var requestedLocation = next.$$route.originalPath;
        var isDataChanged = $(document).data("changed");
        if (isDataChanged == true) {
            jsonRemoveMessage();
            var onSuccess = function () {
                $(document).data("changed", false);
                $rootScope.$apply(function () {
                    $location.url(requestedLocation);
                });
            }


            confirmBox(onSuccess);
            $('.confirm').trigger("click");
            $('.confirm').remove();
            $('body').append('<a href="javascript:void(0);" class="confirm" style="display:none;" rel="confirm-6">Show</a>');
            event.preventDefault();
            return false;
        }

        if ($rootScope.progress < 1 && requestedLocation != "/" && requestedLocation != "") {
            jsonErrorMessage("Please add Basic Info First.");
            $location.url('/');
        }

        if ($rootScope.progress == 1 && requestedLocation != "/" && requestedLocation != "/activity-brief") {
            jsonErrorMessage("Please add Activity Brief to Complete Your Activity Or Your Activity Will Be Save as Draft.");
            $location.url('/activity-brief');
        }

    });

    $rootScope.$on("$routeChangeSuccess", function (event, current, previous) {

        //jsonRemoveMessage();
    });



});

/*
 * Js Functions
 */

function getSubService(obj) {
    $.ajax({
        url: fcom.makeUrl('services', 'sub-service'),
        type: 'post',
        data: {'service_id': $(obj).val()},
        success: function (json) {
            json = $.parseJSON(json);
            $('#subcat-list').html(json.msg);
        }
    });
}

function changeBooking(obj) {
    if ($(obj).val() == 100) {
        $("#booking-day-field").parents('.field-set').show();

    } else {
        $("#booking-day-field").parents('.field-set').hide();
    }
    return;
}
function changeDuration(obj) {
    if ($(obj).val() == 100) {
        $("#duration-day-field").parents('.field-set').show();
    } else {
        $("#duration-day-field").parents('.field-set').hide();
    }
    return;
}

function bindImageUploader() {
    $('.modaal-ajax').modaal({
        type: 'ajax',
        after_open: function () {
            var settings = {
                'aspectRatio': 16 / 9,
                'url': fcom.makeUrl('croper', 'activityImage'),
                'afterSaveCallback': function () {

                    $('.modaal-ajax').modaal('close');
                    step2();

                }
            }
            var croper = new CropAvatar(settings);
        }
    });

}
;

function showMap(lat, lang, dragable) {

    L.mapbox.accessToken = mapbox_access_token;
    map = L.mapbox.map('map', 'mapbox.streets')
            .setView([lat, lang], 12);

    layers = {
        Streets: L.mapbox.tileLayer('mapbox.streets'),
        Outdoors: L.mapbox.tileLayer('mapbox.outdoors'),
        Satellite: L.mapbox.tileLayer('mapbox.satellite')
    };

    layers.Streets.addTo(map);
    L.control.layers(layers).addTo(map);
    marker = L.marker(new L.LatLng(lat, lang), {
        icon: L.mapbox.marker.icon({
            'marker-color': 'ff8888'
        }),
        draggable: true
    });
    marker.bindPopup('The marker is dragable! Move It around. ');
    marker.addTo(map);
    if (dragable == 1) {
        marker.on('dragend', ondragend);

        ondragend();
    } else {
        //	$(".inline").modaal();
    }
}
function ondragend() {
    m = marker.getLatLng();
    $('#act_lat').val(m.lat);
    $('#act_long').val(m.lng);
}

function selectAttr(el) {
    var data_attr = $.parseJSON($(el).attr('data-attr'));

    if ($(el).is(':checked')) {
        $('#attr_file_wrapper_' + data_attr.attr_id).show();
    } else {
        $('#attr_file_wrapper_' + data_attr.attr_id).hide();
    }

}

function actionStep6() {
    jsonStrictNotifyMessage();
    serviceType = $(".service-type:checked").val();
    confrimType = $(".confirm-type:checked").val();
    $('#time-slot-form').ajaxSubmit({
        delegation: true,
        data: {'year': $('.current-yr').text(), 'month': $('.current-mon').text()},
        success: function (json) {
            json = $.parseJSON(json);
            if (json.status == "1") {
                $(document).data("changed", false);
                jsonSuccessMessage(json.msg);
                currentMonth($('.current-yr').text(), $('.current-mon').text());
            } else {
                jsonErrorMessage(json.msg);
                if (json.step != null && json.step == 1) {
                    makeActive(0);
                }

            }
        }
    });
}

function prevMonth(year, month) {
    jsonNotifyMessage('Loading...');
    $.ajax({
        url: fcom.makeUrl('hostactivity', 'step6'),
        type: 'post',
        data: {'year': year, 'month': month, 'type': 'prev'},
        success: function (json) {

            json = $.parseJSON(json);
            if (json.status == 1) {
                jsonRemoveMessage();
                $('.form-section').html(json.html);
				$('.modaal-ajax').modaal({
					type: 'ajax'
				});
				$('.available-instruction-popup').modaal();
				$('.prior-instruction-popup').modaal();
				$('.bulk-entry-instruction-popup').modaal();

            } else
                jsonErrorMessage(json.msg);
        }
    });
}

function nextMonth(year, month) {
    jsonNotifyMessage('Loading...');
    $.ajax({
        url: fcom.makeUrl('hostactivity', 'step6'),
        type: 'post',
        data: {'year': year, 'month': month, 'type': 'next'},
        success: function (json) {

            json = $.parseJSON(json);
            if (json.status == 1) {
                jsonRemoveMessage();
                $('.form-section').html(json.html);
                $('.modaal-ajax').modaal({
					type: 'ajax'
				});
				$('.available-instruction-popup').modaal();
				$('.prior-instruction-popup').modaal();
				$('.bulk-entry-instruction-popup').modaal();

            } else
                jsonErrorMessage(json.msg);
        }
    });
}

function currentMonth(year, month) {
    jsonNotifyMessage('Loading...');
    location.reload(true);
    return;
    fcom.updateWithAjax(fcom.makeUrl('ManageActivity', 'availablity'), {'year': year, 'month': month, 'type': 'current'}, function (json) {
        if (json.status == 1) {
            jsonRemoveMessage();
            $('.form-section').html(json.html);
            $('.modaal-ajax').modaal({
                type: 'ajax'
            });

            $('.available-instruction-popup').modaal();
            $('.prior-instruction-popup').modaal();
            $('.bulk-entry-instruction-popup').modaal();

        } else {
            jsonErrorMessage(json.msg);
        }
    });

    /* $.ajax({
     url: fcom.makeUrl('hostactivity', 'step6'),
     url: fcom.makeUrl('ManageActivity', 'availablity'),
     type: 'post',
     data: {'year': year, 'month': month, 'type': 'current'},
     success: function (json) {
     
     json = $.parseJSON(json);
     if (json.status == 1) {
     jsonRemoveMessage();
     $('.form-section').html(json.html);
     $('.modaal-ajax').modaal({
     type: 'ajax'
     });
     
     } else {
     jsonErrorMessage(json.msg);
     }
     }
     }); */
}

function entryOption(obj) {
    var val = $(obj).val();
    serviceType = $(".service-type:checked").val()
    confrimType = $(".confirm-type:checked").val()
    $('#time-slot > .slots').remove();
    if (val == 1) {
        $("#time-slot").show();
        $("#week-slot").hide();
        $("#notime-slot").hide();
    } else if (val == 2) {
        $("#week-slot").show();
        $("#time-slot").hide();
        $("#notime-slot").hide();
    } else {
        $("#time-slot").hide();
        $("#notime-slot").hide();
        $("#week-slot").hide();
    }

    if (serviceType == 1) {
        $("#time-slot").hide();
        $("#notime-slot").show();
    }
}

function onChangeTimeOption() {

    onChangeTime($(".serviceopt-type:checked").val());

}
function onChangeTime(cVal) {
    if (cVal == 1) {
        $(".time-opt").hide();
    } else {
        $(".time-opt").show();
    }
}

function addMoreTimeSlot() {
    $('#time-slot .form-element__control > div:first').append($('.time-slot-section').html());
}

$(document).on('click', '.remove-slot', function () {
    $(this).parent('div').parent('div').remove();
});

$(document).on('click', '.weekdays', function () {
    time_slot = 0;
    $('.weekdays').each(function () {
        if ($(this).is(':checked')) {
            time_slot = 1;

        }
    });
    if (time_slot == 1) {
        $("#time-slot").show();
    } else {
        $("#time-slot").hide();
    }
    serviceType = $(".service-type:checked").val();
    if (serviceType == 1) {
        $("#time-slot").hide();
        $("#notime-slot").show();
    }
});


function deleteEvent(obj, eventId) {
    if (!confirm('Are You Sure?')) {
        return;
    }
    $.ajax({
        url: fcom.makeUrl('hostactivity', 'delete-event'),
        data: {'event_id': eventId},
        type: 'post',
        success: function (json) {
            json = $.parseJSON(json);
            if (json.status == 1) {
                jsonSuccessMessage(json.msg);
                currentMonth($('.current-yr').text(), $('.current-mon').text());
            } else {
                jsonErrorMessage(json.msg);
            }
        }
    });
}

function addNewEvent() {
    serviceType = $(".service-type:checked").val();
    confrimType = $(".confirm-type:checked").val();
    $('#new-event').ajaxSubmit({
        delegation: true,
        success: function (json) {
            json = $.parseJSON(json);
            if (json.status == "1") {
                $('.modaal-ajax').modaal('close');
                jsonSuccessMessage(json.msg);
                currentMonth($('.current-yr').text(), $('.current-mon').text());
            } else {
                jsonErrorMessage(json.msg);
            }
        }
    });
}

function cleareMonthRecord() {
    if (!confirm('Are You Sure?')) {
        return;
    }
    $.ajax({
        url: fcom.makeUrl('hostactivity', 'delete-all-event'),
        data: {'year': $('.current-yr').text(), 'month': $('.current-mon').text()},
        type: 'post',
        success: function (json) {
            json = $.parseJSON(json);
            if (json.status == 1) {
                jsonSuccessMessage(json.msg);
                currentMonth($('.current-yr').text(), $('.current-mon').text());
            } else {
                jsonErrorMessage(json.msg);
            }
        }
    });
}

function serviceChange() {
    $(".entry-type").val("").change();
    $("#time-slot").hide();
    $("#notime-slot").hide();
}

jQuery.fn.formModfied = function (frm_id) {

    $(document).data("changed", false);

    $('#' + frm_id).on('change', function () {
        $(document).data("changed", true);
    });

};

$(document).on('click', '.js-button-avatar-upload', function () {
    document.getElementById("frm_fat_id_imageUploadFrm").reset();
    $(document).find('.js-avatar-wrapper').empty();
    $(document).find('.js-avatar-preview').empty();
});

$(document).on('keyup change', "input[name$='activity_video']", function () {
    if ($(this).val().length == 0) {
        $(document).data("changed", false);
        $('.erlist_activity_video').hide();
    }
});