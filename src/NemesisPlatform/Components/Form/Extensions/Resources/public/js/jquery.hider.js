/**
 * Created by Pavel on 2014-09-29.
 */

(function ($) {
    $.fn.choice_hider = function (options) {

        var hider_options = options !== undefined ? options : false;

        var $this = $(this);

        var hider_group = $this.data('hider');
        var hider_initiator = $this.data('hider-initiator');
        var hider_sources = $this.data('hider-sources') ? $this.data('hider-sources').toString().split(',') : [];

        if (!hider_options) {
            if (hider_initiator) {

                $this.change(function () {
                    var source = $this.find(':selected').val();
                    $('[data-hider=' + hider_group + ']').not('[data-hider-initiator]').each(function () {
                        $(this).choice_hider({
                            'action': 'show',
                            'source': source
                        });
                    });
                });
            }
        }

        if (hider_options && ('action' in hider_options) && !hider_initiator) {
            if ((hider_options.action === 'show')) {
                if ($.inArray(hider_options.source, hider_sources) === -1) {
                    $(this).closest('.form-group').hide();
                    $(this).data('required', this[0].getAttribute('required') || $(this).data('required') ? this[0].getAttribute('required') || $(this).data('required') : false);
                    console.log($(this).attr('id'), $(this).data('required'));
                    this[0].removeAttribute('required');
                } else {
                    $(this).closest('.form-group').show();
                    console.log($(this).attr('id'), $(this).data('required'));
                    if ($(this).data('required')) {
                        this[0].setAttribute('required', $(this).data('required'));
                        $(this).data('null')
                    }
                }
            }
        }
    };

    $(document).ready(function () {
        $('[data-hider]').each(function () {
            $(this).choice_hider();
        });
        $('[data-hider-initiator]').change();
    });

})(jQuery);

