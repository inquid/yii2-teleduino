/**
 * @author Andriy Kmit' <dev@madand.net>
 * @license http://opensource.org/licenses/MIT
 */

/**
 * JS code for yii-teleduino module.
 */
;
(function ($) {

    var yii_teleduino = window.yii_teleduino = {};
    yii_teleduino.methodsDescriptions = {};

    /**
     * Handler for method select change event.
     * @param e
     */
    yii_teleduino.onSelectChange = function (e) {
        if (e.val != '') {
            var getMethodFormActionUrl = $('#teleduino-get-method-form-url').val();

            // Display selected method's description
            $('#teleduino-method-description').fadeIn()
				.text(yii_teleduino.methodsDescriptions[e.val]);

            // Clear response container
            $('#teleduino-response-container').html('');

            $.post(
                getMethodFormActionUrl,
                {method: e.val, _: (new Date()).getTime()},
                function (html) {
                    $("#teleduino-request-form-container")
                        // Insert received form HTML
                        .html(html)
                        // Show parameter descriptions as tooltips
                        .find("input[type=text], textarea").tooltip(
                            {
                                effect: "slide",
                                position: "top center",
                                predelay: 300
                            }
                        ).dynamic(
                            {
                                bottom: {
                                    offset: [-15, 0]
                                }
                            }
                        );
                }
            )
        }
    };

    // Error handler for jQuery ajax method.
    var ajaxErrorHandler = function (XHR, textStatus, errorThrown) {
        var err;

        if (XHR.readyState === 0 || XHR.status === 0) {
            return;
        }

        switch (textStatus) {
            case 'timeout':
                err = 'The request timed out!';
                break;
            case 'parsererror':
                err = 'Parser error!';
                break;
            case 'error':
                if (XHR.status && !/^\s*$/.test(XHR.status)) {
                    err = 'Error ' + XHR.status;
                } else {
                    err = 'Error';
                }
                if (XHR.responseText && !/^\s*$/.test(XHR.responseText)) {
                    err = err + ': ' + XHR.responseText;
                }
                break;
        }

        alert(err);
    };

    $(document).ready(function ($) {
        $("#teleduino-request-form-container").on("submit", "#request-form", function (e) {
            var $form = $(this);

            e.preventDefault();

            $.ajax($form.attr('action'), {
                cache: false,
                type: 'POST',
                data: $form.serialize(),
                error: ajaxErrorHandler
            }).done(function (html, textStatus) {
                $('#teleduino-response-container').html(html);
            });
        });
    });

})(jQuery);
