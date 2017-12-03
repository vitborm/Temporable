/**
 *  Copyright 2014 Vitaly Bormotov <bormvit@mail.ru>
 *
 *  This file is part of Quilfe Temporable. 
 *
 *  Quilfe Temporable is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Quilfe Temporable is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with Quilfe Temporable. If not, see <http://www.gnu.org/licenses/>.
**/
$(function() {
    ajaxErrorFunction = function (details) {
        var msg = 'При выполнении запроса произошла ошибка';
        if (details && typeof details == 'string')
            msg += '. Подробности: ' + details;
        if (confirm(msg + '. Перезагрузить страницу?'))
            window.location.reload();
    };

    projectRowMessage = function (element, text, mode) {
        if (!mode) {
            mode = 'info';
        }

        if (!element.hasClass('project'))
            element = element.parents('.project');

        element.find('.project-message')
            .stop()
            .css('opacity', 1)
            .text(text)
            .removeClass('good')
            .removeClass('bad')
            .removeClass('info')
            .addClass(mode)
            .show()
            .fadeOut(2000)
        ;
    }

    $('#start-period, #start-work, #stop-work').on('click', function () {
        if (!confirm($(this).text() + '?')) {
            return;
        }

        $.ajax({
            url: $(this).data('url'),
            type: 'POST',
            success: function (data) {
                if (data.status == 'ok')
                    window.location.reload();
                else
                    ajaxErrorFunction(data.status);
            },
            error: ajaxErrorFunction
        });
    });

    $('.projects .project-time input[type="button"]').on('click', function () {
        var input = $(this);
        var projectId = input.parents('.project').data('project');

        input.attr('disabled', 'disabled');
        $.ajax({
            url: $(this).data('url'),
            type: 'POST',
            data: {
                projectId: projectId
            },
            success: function (data) {
                if (data.status == 'ok') {
                    projectRowMessage(input, 'Отмечено время ' + data.time, 'good');
                    input.removeAttr('disabled');
                    $('.work-start-at').text(data.currentTime);
                } else {
                    projectRowMessage(input, data.status, 'bad');
                    input.removeAttr('disabled');
                }
            },
            error: ajaxErrorFunction
        });
    });

    $('.projects .project-time input[type="text"]').on('keyup', function (e) {
        if (e.keyCode != 13)
            return;

        var input = $(this);
        var timeString = input.val(),
            projectId = input.parents('.project').data('project'),
            timeFormatRegex = /^((\d{1,2}\:\d{2})|(\d{1,2}))$/;
        if (!timeFormatRegex.test(timeString)) {
            projectRowMessage(
                input,
                'Укажите целое количество часов или продолжительность в формате ЧЧ:ММ',
                'bad'
            );

            return;
        }

        var separatorPos = timeString.indexOf(':');
        if (-1 === separatorPos) {
            var time = parseInt(timeString) * 60;
        } else {
            var time = parseInt(timeString.substr(0, separatorPos)) * 60 + parseInt(timeString.substr(separatorPos + 1));
        }

        if (!time) {
            projectRowMessage(input, 'Вы указали нулевое время', 'bad');

            return;
        }

        if (time > 24*60) {
            projectRowMessage(input, 'Укажите не больше 24 часов', 'bad');

            return;
        }

        input.attr('disabled', 'disabled');
        $.ajax({
            url: $(this).data('url'),
            type: 'POST',
            data: {
                projectId: projectId,
                time: time
            },
            success: function (data) {
                if (data.status == 'ok') {
                    projectRowMessage(input, 'Время успешно внесено!', 'good');
                    input.removeAttr('disabled').val('');
                } else {
                    input.removeAttr('disabled');
                    ajaxErrorFunction(data.status);
                }
            },
            error: ajaxErrorFunction
        });
    });

    $('.projects .project .project-title .task-opener').on('click', function () {
        var open = $(this).hasClass('open-tasks');
        var project = $(this).parents('.project');
        if (open) {
            project.find('.task-opener.open-tasks').hide();
            project.find('.task-opener.close-tasks').show();

            project.next('.project-tasks').show();
        } else {
            project.find('.task-opener.open-tasks').show();
            project.find('.task-opener.close-tasks').hide();

            project.next('.project-tasks').hide();
        }
    });

    $('.projects .project-tasks .task-adder input.task-name').on('keyup', function (e) {
        if (e.keyCode != 13) {
            return;
        }

        $(this).next('.task-add').click();
    });

    $('.projects .project-tasks input.task-add').on('click', function () {
        var button = $(this);
        var input = button.prev();
        var name = input.val();

        if (!name) {
            alert('Введите текст задачи.');

            return;
        }

        button.attr('disabled', 'disabled');
        input.attr('disabled', 'disabled');

        $.ajax({
            url: $(this).data('url'),
            type: 'POST',
            data: {
                name: name,
            },
            success: function (data) {
                input.parents('.task-adder').after(data);
                button.removeAttr('disabled');
                input.val('');
                input.removeAttr('disabled');
                input.focus();
            },
            error: ajaxErrorFunction
        });
    });

    $('.task-list-container').on('click', 'input.task-mark', function () {
        var button = $(this);

        button.attr('disabled', 'disabled');

        $.ajax({
            url: $(this).data('url'),
            type: 'POST',
            success: function (data) {
                button.parents('.task').replaceWith(data);
            },
            error: ajaxErrorFunction
        });
    });

    $('.task-list-container').on('dblclick', 'span.task-name:not(.task-done):not(.updating)', function () {
        var nameElement = $(this);
        nameElement.addClass('updating');

        var toBeRed = !nameElement.hasClass('task-red');
        if (toBeRed) {
            nameElement.addClass('task-red');
        } else {
            nameElement.removeClass('task-red');
        }

        $.ajax({
            url: $(this).attr('data-red-url'),
            type: 'POST',
            data: {
                'make': toBeRed ? 'red' : 'no',
            },
            success: function (data) {
                nameElement.removeClass('updating');
            },
            error: function (data) {
                if (toBeRed) {
                    nameElement.removeClass('task-red');
                } else {
                    nameElement.addClass('task-red');
                }
                ajaxErrorFunction(data);
                nameElement.removeClass('updating');
            }
        });
    });
});
