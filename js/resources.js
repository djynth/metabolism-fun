var COLOR_INCREASE = "72,144,229";
var COLOR_DECREASE = "232,12,15";
var TRACKER_ICONS = 5;
var TRACKER_WAIT = 300;         // the amount of time between tracker animations, in ms
var TRACKER_ANIMATION = 600;    // the duration of a tracker animtion, in ms

function getRes(resource, organ)
{
    if (typeof organ === 'undefined') {
        var resources = $('.resources');
    } else {
        var resources = $('.resources[organ="' + organ + '"]');
    }
    return resources.find('.res[res="' + resource + '"]').first();
}

function refreshResources(resources)
{
    if (typeof resources === 'undefined') {
        $('.resources').find('.res').each(function() {
            updateRes($(this), parseInt($(this).attr('amount')));
        });
    } else {
        for (var resource in resources) {
            for (var organ in resources[resource]) {
                var amount = resources[resource][organ];
                var res = getRes(resource, organ);
                var change = amount - parseInt(res.attr('amount'));
                if (change === 0) {
                    continue;
                }

                var increase = change > 0 ? 'increase' : 'decrease';
                res.addClass(increase).delay(1000).queue(function() {
                    $(this).removeClass(increase).dequeue();
                });
                res.res(amount);

                updateRes(res, amount);
                updateTracker(resource, organ);
            }
        }
    }
    refreshResourceLimits();
    refreshLimitedResources();
}

function updateRes(res, amount)
{
    res.find('.amount').html(amount);
    res.find('.bar').css('width', Math.min(100, 100*(amount/parseInt(res.attr('max-shown')))) + '%');
}

function refreshResourceLimits()
{
    $('.resources').find('.res').each(function() {
        var maxShown = $(this).attr('max-shown');
        var organ = $(this).organ();
        $(this).find('.limit').each(function() {
            var val1 = null, val2 = null;
            if (typeof $(this).attr('rel-limit') !== "undefined") {
                val1 = getRes($(this).attr('rel-limit'), organ).attr('amount');
            }
            if (typeof $(this).attr('limit') !== "undefined") {
                val2 = $(this).attr('limit');
            }
            if (val1 === null && val2 === null) {
                return;
            }

            if ($(this).hasClass('max')) {
                $(this).width(min(100, 100*(maxShown - min(val1, val2))/maxShown) + "%");
            } else {
                $(this).width(max(0, 100*max(val1, val2)/maxShown) + "%");
            }
        });
    });
}

jQuery.fn.extend({
    addResourceInfoSources: function() {
        this.find('.res-info-source').click(function() {
            var visual = $('#resource-visual');
            var res = $(this).res();
            if (visual.res() !== res) {
                if (visual.res()) {
                    visual.fadeOut(function() {
                        updateResourceVisual(res, visual, function() {
                            visual.finish().fadeIn();
                        });
                    });
                } else {
                    updateResourceVisual(res, visual, function() {
                        visual.finish().fadeIn();
                    });
                }
            }
        });

        return this;
    }
});
