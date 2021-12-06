//NOTE po wybraniu pierwszej opcji, drugi select przeładowuje sie

$(document).ready(function() {
    var $locationSelect = $('.js-article-form-location'); //pierwszy seelct
    var $specificLocationTarget = $('.js-specific-location-target'); //element znajdujący się wokół drugiego pola

    $locationSelect.on('change', function(e) {
        $.ajax({
            url: $locationSelect.data('specific-location-url'),
            data: {
                location: $locationSelect.val()
            },
            success: function (html) {
                if (!html) { //NOTE if response is empty
                    $specificLocationTarget.find('select').remove();
                    $specificLocationTarget.addClass('d-none');

                    return;
                }

                // Replace the current field and show
                $specificLocationTarget
                    .html(html)
                    .removeClass('d-none')
            }
        });
    });
});
