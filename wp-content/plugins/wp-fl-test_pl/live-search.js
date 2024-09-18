jQuery(document).ready(function($) {
    function performSearch(page = 1) {
        var query = $('#live-search-input').val();
        var acf_criteria = {};

        $('#acf-criteria input').each(function() {
            var rawFieldName = $(this).attr('name');
            var value = $(this).val();

            // Clean the field name by removing 'acf_criteria[' and ']'
            var cleanedFieldName = rawFieldName.replace('acf_criteria[', '').replace(']', '');

            if (value) {
                acf_criteria[cleanedFieldName] = value;
            }
        });

        $.ajax({
            url: liveSearchParams.ajax_url,
            type: 'POST',
            data: {
                action: 'live_search',
                query: query,
                acf_criteria: acf_criteria,
                paged: page 
            },
            success: function(response) {
                var data = JSON.parse(response);
                if (data.content) {
                    $('main').html(data.content); 
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX Error:', textStatus, errorThrown);
            }
        });
    }

    $('#live-search-input').on('keyup', function(e) {
        if (e.key === 'Enter') {
            performSearch();
        }
    });

    $('#live-search-submit').on('click', function() {
        performSearch();
    });

    $(document).on('click', '.live-search-page', function(e) {
        e.preventDefault();
        var page = $(this).data('page'); 
        performSearch(page); 
    });
});
