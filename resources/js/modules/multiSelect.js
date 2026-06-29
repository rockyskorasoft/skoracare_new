const initMultiSelect = () => {
    if (typeof $.fn.select2 !== 'function') {
        return;
    }

    $('.multiSelectSearch').each(function () {
        const $element = $(this);
        const searchUrl = $element.data('search-url');

        if ($element.hasClass('select2-hidden-accessible')) {
            $element.select2('destroy');
        }

        const options = {
            placeholder: $element.data('placeholder') || 'Select',
            allowClear: true,
            width: '100%',
            closeOnSelect: false,
            maximumSelectionLength: 10,
        };

        if (searchUrl) {
            options.minimumInputLength = 1;
            options.ajax = {
                url: searchUrl,
                dataType: 'json',
                delay: 250,
                cache: true,
                data: function (params) {
                    return {
                        term: params.term || '',
                        q: params.term || '',
                        page: params.page || 1,
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;

                    return {
                        results: data.results || [],
                        pagination: {
                            more: Boolean(data.pagination && data.pagination.more),
                        },
                    };
                },
            };
        }

        $element.select2(options);
    });
};

$(initMultiSelect);
