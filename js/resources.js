var COLOR_INCREASE = "72,144,229";
var COLOR_DECREASE = "232,12,15";

$(document).ready(function() {
    refreshResources();

    $('.resource-data').mouseenter(function() {
        var resource = $(this).attr('value');

        $('#resource-visual').append('<img name="' + resource + '" src="' + baseUrl + 'img/molecules/' + resource + '"'
            + 'alt="' + resource + '" class="image-content hidden">');

        setTimeout(function() {
            $('#resource-visual img[name="' + resource + '"]').fadeIn(250);
        }, 300);
    });

    $('.resource-data').mouseleave(function() {
        var resource = $(this).attr('value');

        $('#resource-visual img[name="' + resource + '"]').fadeOut(100, function() {
            $(this).remove();
        });
    });
});

function refreshResources(resources)
{
    if (typeof resources === 'undefined') {
        $('.resource-holder .resource-data').each(function() {
            onResourceChange(
                $(this).attr('value'),
                $(this).parents('.resource-holder').attr('value'),
                parseInt($(this).find('.resource-value').html())
            );
        });
        refreshPathways();
        initScrollbars();
    } else {
        for (var resource in resources) {
            for (var organ in resources[resource]) {
                var value = resources[resource][organ];
                onResourceChange(resource, organ, value);
            }
        }
        refreshPathways();
    }
}

function onResourceChange(resource, organ, value)
{
    var change = value - getResourceValue(resource, organ);
    var elem = getResourceElement(resource, organ);
    var color = change > 0 ? COLOR_INCREASE : COLOR_DECREASE;
    if (change == 0) {
        if (elem.attr('init')) {
            color = false;
            elem.removeAttr('init');
        } else {
            return;
        }
    }
    
    if (color) {
        elem.animate({ boxShadow : "0 0 5px 5px rgb("+color+")" }, function() {
            elem.animate({ boxShadow : "0 0 5px 5px rgba("+color+", 0)" });
        });
    }

    elem.find('.resource-value').html(value);
    elem.find('.bar').css('width', Math.min(100, 100*(value/parseInt(elem.attr('max-shown')))) + '%');
}

function getResourceElement(resource, organ)
{
    return $('.resource-holder[value="' + organ + '"] .resource-data[value="' + resource + '"]');
}

function getResourceValue(resource, organ)
{
    return parseInt(getResourceElement(resource, organ).find('.resource-value').html());
}

function getResourceName(resource, organ)
{
    return getResourceElement(resource, organ).find('.resource-name').html();
}
