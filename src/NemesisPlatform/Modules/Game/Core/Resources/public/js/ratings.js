/**
 * Created by Pavel Batanov <pavel@batanov.me> on 30.01.14.
 */

(function ($) {

    function renderRatingsGraph(container, data, options) {

        var series = [];

        console.log(data);

        $.each(data, function (k, i) {
            var series_line = {};
            series_line.name = i.name;
            series_line.data = [];
            console.log(i);
            $.each(i.data, function (dk, di) {
                series_line.data.push(["Период " + dk, di]);
            });
            series.push(series_line);
        });


        container.highcharts({
            title: {
                text: '<span class="text-muted">#' + options['round_id'] + '</span> ' + options['round_name'],
                x: -20 //center
            },
            yAxis: {
                title: null
            },
            xAxis: {
                categories: [
                    'История',
                    '1 Период',
                    '2 Период',
                    '3 Период',
                    '4 Период',
                    '5 Период'
                ]
            },
            tooltip: {shared: true},
            legend: {
                layout: 'vertical',
                align: 'left',
                verticalAlign: 'middle',
                borderWidth: 0
            },
            series: series
        });
    }


    var table_constructor = function (data, team_route) {
        var $table = $('<table/>', {'class': "table table-responsive table-bordered table-striped"});
        var $headrow = $('<tr/>').append($('<th/>').html('Команда'));
        var $thead = $('<thead/>').append($headrow);
        var $tbody = $('<tbody/>');

        var known_periods = [];

        $.each(data, function (company, company_data) {
            var $company_row = $('<tr/>');
            $company_row.append(
                $('<td/>')
                    .append($('<a/>',
                        {
                            'href': team_route.replace('_id_', company_data.id),
                            'html': company_data.name
                        }
                    ))
            );
            var bV = -1;
            $.each(company_data.data, function (period, period_value) {


                if ($.inArray(period, known_periods) < 0) {
                    $headrow.append($('<th/>').html(period));
                    known_periods.push(period);
                }

                var $p_val = $('<td/>', {'class': 'rating-value'}).html(period_value);

                if (bV == -1) {
                    bV = period_value;
                    $p_val.addClass('rating-base');
                } else {
                    if (period_value > bV) $p_val.addClass('rating-gain');
                    if (period_value < bV) $p_val.addClass('rating-lost');
                    if (period_value == bV) $p_val.addClass('rating-save');
                    bV = period_value;
                }
                $company_row.append($p_val);

            });
            $tbody.append($company_row);
        });

        $.each(known_periods, function (period) {
            var $rows = $tbody.find('tr');
            var $column = [];
            var values = [];
            $rows.each(function () {
                var $td = $(this).find('td').eq(period + 2);
                $column.push($td);
                values.push($td.html());
            });

            $.each($column, function () {
                if ($(this).html() == Math.max.apply(Math, values)) {
                    $(this).addClass('rating-max');
                }
            })
        });

        $table.append($thead);
        $table.append($tbody);

        return $table
    };

    $.fn.ratings_table = function () {
        var $this = $(this);

        var build_ratings = function () {
            $.getJSON(
                $this.data('ratings-source'),
                {
                    'round': $($this.data('round')).find(':selected').val(),
                    'league': $($this.data('league')).val(),
                    'group': $($this.data('group')).val()
                },
                function (data) {
                    $this.html(table_constructor(data, $this.data('team-route')));
                    renderRatingsGraph(
                        $('#ratings-graph'),
                        data,
                        {
                            'round_id': $($this.data('round')).find(':selected').val(),
                            'round_name': $($this.data('round')).find(':selected').html(),
                            'league': $($this.data('league')).val(),
                            'group': $($this.data('group')).val()
                        });
                }
            );
        };

        $($this.data('toggle')).click(build_ratings);
        $(($this.data('round'))).change(build_ratings);
        $(($this.data('league'))).change(build_ratings);
        $(($this.data('group'))).change(build_ratings);
    };

    $('[data-ratings-source]').ratings_table();
})(jQuery);
