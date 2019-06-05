$(function () {

    // 初始化

    // 杂项
    var height = window.innerHeight;
    $('#container').css('height', height);
    $('#logo img').css('padding-top', height * 0.1).css('min-height', height * 0.45);
    // 窗口大小变换
    window.onresize = function () {
        var height = window.innerHeight;
        $('#container').css('height', height);
        $('#logo img').css('padding-top', height * 0.1).css('min-height', height * 0.45);
    };

    // 头像
    for (var key in style) {
        var img = $('<img class="avatar" src="" alt="">').attr('src', AVATAR + style[key] + ".png").text(key);
        $('#avatar').append(img);

        img.click(function () {
            var _this = $(this);

            $('.avatar').each(function () {
                if ($(this).hasClass('active')) {
                    $(this).removeClass('active');
                }
            });

            _this.addClass('active');
            // 更改主题
            user.style = _this.text();
        });
    }
    $('.avatar').first().addClass('active');

    // TODO:IP地址

});

// 设置头像及主题
function setStyle() {
    // 菜单的显示切换
    if ($('#avatar').css('display') == 'none') {
        $('#avatar').css('display', 'block');
    } else {
        $('#avatar').css('display', 'none');
    }
}
