var MENU;
var INTERNAL_ERROR = 'An internal error occurred.';

$(document).ready(function() {
    MENU = $('#menu');

    MENU.find('.tab').click(function() {
        if (!$(this).hasClass('active')) {
            MENU.find('.tab.active').removeClass('active');
            MENU.find('.content.active').removeClass('active');

            $(this).addClass('active');
            MENU.find('.content.' + $(this).attr('for')).addClass('active').find('input[type=text], input[type=passwprd]').first().focus();
        }
    });

    MENU.find('.theme').find('.select').click(function() {
        var theme = $(this).parents('.theme');
        setColorTheme(theme.attr('theme'), theme.attr('type'), true);
    });

    MENU.find('.form').find('input[type=text], input[type=password]')
        .focusin(function() {
            $(this).parents('.form').find('.form-info')
                .removeClass('error')
                .addClass('active')
                .html($(this).attr('info'));
        })
        .focusout(function() {
            $(this).parents('.form').find('.form-info')
                .removeClass('error')
                .removeClass('active')
                .html('');
        })
        .change(function() {
            if ($(this).hasClass('username') && $(this).attr('verify') !== 'no') {
                validate($(this), 'username');
            }
            if ($(this).hasClass('email') && $(this).attr('verify') !== 'no') {
                validate($(this), 'email');
            }
            if ($(this).hasClass('new-password') && $(this).attr('verify') !== 'no') {
                validate($(this), 'password');
            }

            if ($(this).hasClass('confirm') || $(this).hasClass('new-password')) {
                var password = $(this).parent().siblings('.new-password');
                var confirm = $(this).parent().children('.confirm')
                confirm.toggleClass('error', !match(confirm, password));
            }
        });

    $('#login').submit(function() {
        var form = $(this);
        if (form.find('input.error').length === 0) {
            $.ajax({
                url: '/index.php/user/login',
                type: 'POST',
                dataType: 'json',
                data: {
                    username: $(this).find('.username').val(),
                    password: $(this).find('.password').val()
                },
                success: function(data) {
                    if (data.success) {
                        location.reload();
                    } else {
                        form.siblings('.form-info')
                            .addClass('active error')
                            .html(data.message);
                    }
                },
                error: function() {
                    form.siblings('.form-info')
                        .addClass('active error')
                        .html(INTERNAL_ERROR);
                }
            });
        }
        return false;
    });

    $('.forgot-password').find('input[type=button]').click(function() {
        var form = $(this).parents('form');
        $.ajax({
            url: '/index.php/user/forgotPassword',
            type: 'POST',
            dataType: 'json',
            data: {
                username: form.find('.username').val()
            },
            success: function(data) {
                form.siblings('.form-info')
                    .addClass('active' + (data.success ? '' : ' error'))
                    .html(data.message);
            },
            error: function() {
                form.siblings('.form-info')
                    .addClass('active error')
                    .html(INTERNAL_ERROR);
            }
        });
    });

    $('#create-account').submit(function() {
        var form = $(this);
        if (form.find('input.error').length === 0) {
            var theme = getColorTheme();
            $.ajax({
                url: '/index.php/user/createAccount',
                type: 'POST',
                dataType: 'json',
                data: {
                    username:   $(this).find('.username').val(),
                    password:   $(this).find('.new-password').val(),
                    email:      $(this).find('.email').val(),
                    theme:      theme.theme,
                    theme_type: theme.type
                },
                success: function(data) {
                    if (data.success) {
                        location.reload();
                    } else {
                        form.siblings('.form-info')
                            .addClass('active error')
                            .html(data.message);
                    }
                },
                error: function() {
                    form.siblings('.form-info')
                        .addClass('active error')
                        .html(INTERNAL_ERROR);
                }
            });
        }
        return false;
    });
});

function match(password, confirm)
{
    return password.val() == confirm.val() || password.val() == '' ||
           confirm.val() == '';
}

function validate(input, type)
{
    input.removeClass('error');
    if (input.val()) {
        $.ajax({
            url: '/index.php/user/validate',
            type: 'POST',
            dataType: 'json',
            context: $(this),
            data: {
                type: type,
                value: input.val()
            },
            success: function(data) {
                input.toggleClass('error', !data.valid);
            }
        });
    }
}
