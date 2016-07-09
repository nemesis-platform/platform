/**
 * Created by Pavel on 2014-09-07.
 */

(function ($) {
    $.fn.entity_autocomplete = function () {
        var $this = $(this);
        var $storage = $($this.data('autocomplete-storage'));
        var data_source = $this.data('autocomplete');

        var data_source_label_field = $this.data('autocomplete-label') ? $this.data('autocomplete-label') : 'label';
        var data_source_id_field = $this.data('autocomplete-id') ? $this.data('autocomplete-id') : 'id';

        var autocomplete_group = $this.data('autocomplete-group') ? $this.data('autocomplete-group') : $this.data('autocomplete-storage');
        var $group = $('[data-autocomplete-group=' + autocomplete_group + ']');

        $this.autocomplete({
            minLength: 1,
            source: function (request, response) {
                var data = {term: request.term, term_group: autocomplete_group, g_terms: {}};
                $group.find('[data-autocomplete-term]').each(function () {
                    data.g_terms[$(this).data('autocomplete-term')] = $($(this).data('autocomplete-storage')).val();
                });
                $.ajax({
                    url: data_source,
                    dataType: "json",
                    data: data,
                    success: function (data) {
                        response(data);
                    }
                });
            },
            change: function (event, ui) {
                if (ui.item) {
                    $this.val(ui.item[data_source_label_field]);
                    $storage.val(ui.item[data_source_id_field]);
                } else {
                    $this.val(null);
                    $storage.val(null);
                }

            },
            select: function (event, ui) {
                $storage.val(ui.item[data_source_id_field]);
                $group.find('[data-autocomplete-term]').each(function () {
                    if (ui.item[$(this).data('autocomplete-term')] !== undefined) {
                        $(this).val(ui.item[$(this).data('autocomplete-term')][data_source_label_field]);
                        $($(this).data('autocomplete-storage')).val(ui.item[$(this).data('autocomplete-term')][data_source_id_field])
                    }
                });
            },
            messages: {
                noResults: '',
                results: function () {
                }
            },
            _renderItem: function (ul, item) {
                return $("<li>")
                    .append("<a>", {'html': item[data_source_label_field]})
                    .appendTo(ul);
            }
        });
    };

    /**
     * Automatically enable this plugin with 'data-autocomplete' trigger
     */
    $(document).ready(function () {
        $('[data-autocomplete]').each(function () {
            $(this).entity_autocomplete();
        });
        $('[data-prototype]').closest('form').on('DOMNodeInserted', function () {
            $(this).closest('form').find('[data-autocomplete]').each(function () {
                $(this).entity_autocomplete();
            });
        });
        $('form').on('DOMNodeInserted', function () {
            $(this).closest('form').find('[data-autocomplete]').each(function () {
                $(this).entity_autocomplete();
            });
        });
    });

}(jQuery));

