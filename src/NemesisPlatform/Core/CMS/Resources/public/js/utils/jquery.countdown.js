/**
 * Created by scaytrase on 10.06.2014.
 */


(function ($) {
    $.fn.countdown = function (_options) {
        $(this).each(function () {
            var $this = $(this);
            console.log($this);

            _options = typeof _options !== 'undefined' ? _options : [];

            var options = $.merge({
                'countdown': $this.data('countdown'),
                'format': $this.data('format') ? $this.data('format') : '%%D%% d, %%H%%:%%M%%:%%S%%',
                'expire_message': $this.data('expire-message') ? $this.data('expire-message') : ''
            }, _options);

            var update_timer = function ($container) {
                var timeLeft = Math.floor((new Date(new Date(options.countdown) - new Date()).valueOf() ) / 1000);
                if (timeLeft > 0) {
                    var pad = "00";
                    var pl = -pad.length;
                    var format = options.format;
                    $container.html(
                        format
                            .replace(/%%D%%/g, (Math.floor(timeLeft / 86400) % 100000).toString())
                            .replace(/%%H%%/g, (pad + ((Math.floor(timeLeft / 3600)) % 24).toString()).slice(pl))
                            .replace(/%%M%%/g, (pad + ((Math.floor(timeLeft / 60)) % 60).toString()).slice(pl))
                            .replace(/%%S%%/g, (pad + ((Math.floor(timeLeft)) % 60).toString()).slice(pl))
                    );

                } else {
                    clearInterval(timer);
                    $container.html(options.expire_message);
                }
            };

            var timer;
            timer = setInterval(function () {
                update_timer($this);
            }, 1000);
            update_timer($this);
        });
    };
    $('[data-countdown]').countdown();
})(jQuery);
