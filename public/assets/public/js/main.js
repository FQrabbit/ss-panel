$('.dropdown').on('show.bs.dropdown', function() {
    $(this).find('.dropdown-menu').first().stop(true, true).fadeIn();
});
$('.dropdown').on('hide.bs.dropdown', function() {
    $(this).find('.dropdown-menu').first().stop(true, true).fadeOut();
});

$("#msg-success").hide();
$("#msg-error").hide();

$('.sidebar-menu>li').on('click',function(){
    $('.sidebar-menu>li.active').removeClass('active');
    $(this).addClass('active');
})

// Node Page
function urlChange(id) {
    var site = './node/'+id;
    document.getElementById('infoifram').src = site;
    $("#nodeinfo").modal();
    $('html').css('overflow-y', 'hidden');
}

(function(){

$(".close,.modal").click(function(){
    $("#nodeinfo").modal("hide");
    $('html').css('overflow-y', 'auto');
})

})();

$(document).ready(function () {
	// Profile Page
	(function(){
    $("#pwd-update").click(function () {
        $.ajax({
            type: "POST",
            url: "password",
            dataType: "json",
            data: {
                oldpwd: $("#oldpwd").val(),
                pwd: $("#pwd").val(),
                repwd: $("#repwd").val()
            },
            success: function (data) {
                if (data.ret) {
                    $("#psw-msg-success").show(500, function(){
                        window.setTimeout("location.reload()",5000);
                    });
                    $("#psw-msg-success-p").html(data.msg);
                } else {
                    $("#psw-msg-error").show(500, function(){
                        $(this).delay(3000).hide(500);
                    });
                    $("#psw-msg-error-p").html(data.msg);
                }
            },
            error: function (jqXHR) {
                alert("发生错误：" + jqXHR.status);
            }
        })
    })

    $("#email-update").click(function () {
        $.ajax({
            type: "POST",
            url: "email",
            dataType: "json",
            data: {
                email: $("#email").val(),
                verifycode: $("#verifycode").val(),
                reemail: $("#reemail").val()
            },
            success: function (data) {
                if (data.ret) {
                    $("#email-msg-success").show(500, function(){
                        $(this).delay(3000).hide(500);
                    });
                    $("#email-msg-success-p").html(data.msg);
                } else {
                    $("#email-msg-error").show(500, function(){
                        $(this).delay(3000).hide(500);
                    });
                    $("#email-msg-error-p").html(data.msg);
                }
            },
            error: function (jqXHR) {
                alert("发生错误：" + jqXHR.status);
            }
        })
    })

    $("#sendcode").on("click", function () {
        var count = sessionStorage.getItem('email-code-count') || 0;
        var timer, countdown = 60, $btn = $(this);
        if (count > 3 || timer) return false;

        if (!email) {
            $("#email-msg-error").show(500, function(){
                $(this).delay(3000).hide(500);
            });
            $("#email-msg-error-p").html("请先填写邮箱!");
            return $("#email").focus();
        }

        $.ajax({
            type: "POST",
            url: "sendcode",
            dataType: "json",
            data: {
                email: $("#email").val(),
            },
            success: function (data) {
                if (data.ret == 1) {
                    $("#email-msg-success").show(500, function(){
                        $(this).delay(3000).hide(500);
                    });
                    $("#email-msg-success-p").html(data.msg);
                    timer = setInterval(function () {
                        --countdown;
                        if (countdown) {
                            $btn.text('重新发送 (' + countdown + '秒)');
                        } else {
                            clearTimer();
                        }
                    }, 1000);
                } else {
                    $("#email-msg-error").show(500, function(){
                        $(this).delay(3000).hide(500);
                    });
                    $("#email-msg-error-p").html(data.msg);
                    clearTimer();
                }
            },
            error: function (jqXHR) {
                $("#email-msg-error").show(500, function(){
                    $(this).delay(3000).hide(500);
                });
                $("#email-msg-error-p").html("发生错误：" + jqXHR.status);
                clearTimer();
            }
        });
        $btn.addClass("disabled").prop("disabled", true).text('发送中...');
        $("#verifycode").select();
        function clearTimer() {
            $btn.text('重新发送').removeClass("disabled").prop("disabled", false);
            clearInterval(timer);
            timer = null;
        }
    });

    $("#config-update").click(function () {
        $.ajax({
            type: "POST",
            url: "ssconfig",
            dataType: "json",
            data: {
                sspwd: $("#sspwd").val(),
                method: $("#method").val(),
                protocol: $("#protocol").val(),
                obfs: $("#obfs").val(),
                obfs_param: $("#obfs_param").val()
            },
            success: function (data) {
                if (data.ret) {
                    $("#config-msg-success").show(500, function(){
                        window.setTimeout("location.reload()",5000);
                    });
                    $("#config-msg-success-p").html(data.msg);
                } else {
                    $("#config-msg-error").show(500, function(){
                        $(this).delay(3000).hide(500);
                    });
                    $("#config-msg-error-p").html(data.msg);
                }
            },
            error: function (jqXHR) {
                alert("发生错误：" + jqXHR.status);
            }
        })
    })

    $("#portreset").click(function () {
        $.ajax({
            type: "POST",
            url: "resetport",
            dataType: "json",
            success: function (data) {
                if (data.ret) {
                    $("#config-msg-success").show(500, function(){
                        window.setTimeout("location.reload()",5000);
                    });
                    $("#config-msg-success-p").html(data.msg);
                } else {
                    $("#config-msg-error").show(500, function(){
                        window.setTimeout("location.reload()",5000);
                    });
                    $("#config-msg-error-p").html(data.msg);
                }
            },
            error: function (jqXHR) {
                alert("发生错误：" + jqXHR.status);
            }
        })
    })
	})();
}) //document ready

// 
