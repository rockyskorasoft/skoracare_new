$(function () {
    const $formConfig = $('#order-form-config');
    const $filterConfig = $('#order-filter-config');

    function parseData($element, key, fallback = {}) {
        const value = $element.data(key);

        if (!value) {
            return fallback;
        }

        if (typeof value === 'object') {
            return value;
        }

        try {
            return JSON.parse(value);
        } catch (error) {
            return fallback;
        }
    }

    function optionHtml(options, selectedValue) {
        return options.map(function (option) {
            const selected = String(option.id) === String(selectedValue) ? ' selected' : '';
            return '<option value="' + option.id + '"' + selected + '>' + option.label + '</option>';
        }).join('');
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatAddressHtml(addressText) {
        return escapeHtml(addressText).replace(/\n/g, '<br>');
    }

    function formatMoney(value) {
        return Number(value || 0).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
    }

    function moneyInputGroup(inputClass, attributes, value) {
        return '<div class="input-group">'
            + '<span class="input-group-text">$</span>'
            + '<input type="text" class="form-control ' + inputClass + '" ' + (attributes || '') + ' value="' + (value || '') + '" readonly>'
            + '</div>';
    }

    function initOrderFilter() {
        if ($filterConfig.length === 0) {
            return;
        }

        const pharmaciesByProvider = parseData($filterConfig, 'pharmaciesByProvider');
        const selectLabel = $filterConfig.data('selectLabel') || 'Select';
        const $providerSelect = $('#tenant_id');
        const $pharmacySelect = $('#pharmacies_id');

        if ($providerSelect.length === 0 || $pharmacySelect.length === 0) {
            return;
        }

        $providerSelect.on('change', function () {
            const providerId = $(this).val();
            let options = '<option value="" disabled selected>' + selectLabel + '</option>';

            $.each(pharmaciesByProvider[providerId] || [], function (index, pharmacy) {
                options += '<option value="' + pharmacy.id + '">' + pharmacy.label + '</option>';
            });

            $pharmacySelect.html(options);
        });
    }

    function initOrderForm() {
        if ($formConfig.length === 0 || $('#order-items-table').length === 0) {
            return;
        }

        let rowIndex = Number($formConfig.data('rowIndex') || 0);
        const productsByProvider = parseData($formConfig, 'productsByProvider', {});
        const addressesByProvider = parseData($formConfig, 'addressesByProvider', {});
        const pricesByProvider = parseData($formConfig, 'productPricesByProvider', {});
        const selectedProvider = String($formConfig.data('selectedProvider') || '');
        const selectedOrderAddress = String($formConfig.data('selectedOrderAddress') || '');
        const selectedPharmacyId = String($formConfig.data('selectedPharmacyId') || '');
        const selectLabel = $formConfig.data('selectLabel') || 'Select';
        const priceLabel = $formConfig.data('priceLabel') || 'Price';
        const removeLabel = $formConfig.data('removeLabel') || 'Remove';
        const noAddressLabel = $formConfig.data('noAddressLabel') || 'No address found';

        const $tableBody = $('#order-items-table tbody');
        const $providerSelect = $('#tenant_id');
        const $addressCard = $('#provider-address-card');
        const $addressContent = $('#provider-address-content');
        const $orderAddressInput = $('#order_address');
        const $pharmacyIdInput = $('#pharmacies_id');

        function currentProviderId() {
            return $providerSelect.length ? String($providerSelect.val() || '') : selectedProvider;
        }

        function currentProducts() {
            return productsByProvider[currentProviderId()] || [];
        }

        function currentAddresses() {
            return addressesByProvider[currentProviderId()] || [];
        }

        function selectAddress(address, pharmacyId) {
            $orderAddressInput.val(address || '');
            $pharmacyIdInput.val(pharmacyId || '');
        }

        function selectAddressByKey(addressKey) {
            const addressOption = currentAddresses().find(function (option) {
                return option.key === addressKey;
            });

            if (!addressOption) {
                selectAddress('', '');
                return;
            }

            selectAddress(
                addressOption.address,
                addressOption.pharmacy_id ? String(addressOption.pharmacy_id) : ''
            );
        }

        function renderAddressOptions() {
            const addresses = currentAddresses();

            if (!currentProviderId()) {
                $addressCard.addClass('d-none');
                selectAddress('', '');
                return;
            }

            $addressCard.removeClass('d-none');

            if (addresses.length === 0) {
                $addressContent.html('<span class="text-muted">' + escapeHtml(noAddressLabel) + '</span>');
                selectAddress('', '');
                return;
            }

            let html = '';
            let matchedSelection = false;
            let defaultKey = addresses[0].key;

            addresses.forEach(function (addressOption) {
                const pharmacyId = addressOption.pharmacy_id ? String(addressOption.pharmacy_id) : '';
                const isSelected = selectedOrderAddress
                    ? addressOption.address === selectedOrderAddress
                    : (selectedPharmacyId
                        ? pharmacyId === selectedPharmacyId
                        : false);

                if (isSelected) {
                    matchedSelection = true;
                    defaultKey = addressOption.key;
                }

                html += '<div class="form-check border rounded p-3 mb-2">'
                    + '<input class="form-check-input order-address-option" type="radio" name="order_address_option"'
                    + ' id="order-address-' + escapeHtml(addressOption.key) + '"'
                    + ' value="' + escapeHtml(addressOption.key) + '"'
                    + (isSelected ? ' checked' : '')
                    + '>'
                    + '<label class="form-check-label w-100" for="order-address-' + escapeHtml(addressOption.key) + '">'
                    + formatAddressHtml(addressOption.address)
                    + '</label>'
                    + '</div>';
            });

            $addressContent.html(html);

            if (!matchedSelection) {
                $addressContent.find('.order-address-option[value="' + defaultKey + '"]').prop('checked', true);
            }

            selectAddressByKey($addressContent.find('.order-address-option:checked').val() || defaultKey);

            $addressContent.find('.order-address-option').off('change.order').on('change.order', function () {
                selectAddressByKey($(this).val());
            });
        }

        function productSelectHtml(selectedValue) {
            return '<option value="">' + selectLabel + '</option>' + optionHtml(currentProducts(), selectedValue);
        }

        function refreshProductSelect($select, selectedValue) {
            $select.html(productSelectHtml(selectedValue || ''));
        }

        function applyProductPrice($row) {
            const productId = $row.find('.order-product').val();
            const providerPrices = pricesByProvider[currentProviderId()] || {};

            if (productId && providerPrices[productId] !== undefined) {
                const price = Number(providerPrices[productId]);
                $row.find('.order-price').val(price.toFixed(2));
                $row.find('.order-price-display').val(formatMoney(price));
            } else {
                $row.find('.order-price').val('');
                $row.find('.order-price-display').val('');
            }
        }

        function recalculate() {
            let total = 0;

            $tableBody.find('tr').each(function () {
                const $row = $(this);
                const quantity = Number($row.find('.order-quantity').val() || 0);
                const price = Number($row.find('.order-price').val() || 0);
                const subtotal = quantity * price;

                total += subtotal;
                $row.find('.order-subtotal').val(formatMoney(subtotal));
            });

            $('#order-total').val(formatMoney(total));
        }

        function clearRowServerErrors($row) {
            if ($row.find('.order-product').val()) {
                $row.find('td').first().find('.invalid-feedback').addClass('d-none');
            }

            if ($row.find('.order-price').val()) {
                $row.find('.order-price-display').closest('td').find('.invalid-feedback').addClass('d-none');
            }
        }

        function bindRow($row) {
            $row.find('.order-product').off('change.order').on('change.order', function () {
                clearRowServerErrors($row);
                applyProductPrice($row);
                recalculate();
            });

            $row.find('.order-quantity').off('input.order').on('input.order', recalculate);

            $row.find('.remove-order-item').off('click.order').on('click.order', function () {
                if ($tableBody.find('tr').length > 1) {
                    $row.remove();
                    recalculate();
                }
            });
        }

        function refreshProductsForProvider() {
            $tableBody.find('tr').each(function () {
                const $row = $(this);
                const selectedProduct = $row.find('.order-product').val();
                const products = currentProducts();
                const productStillAvailable = products.some(function (product) {
                    return String(product.id) === String(selectedProduct);
                });

                refreshProductSelect($row.find('.order-product'), productStillAvailable ? selectedProduct : '');
                applyProductPrice($row);
            });

            recalculate();
        }

        function refreshProviderData() {
            renderAddressOptions();
            refreshProductsForProvider();
        }

        $('#add-order-item').on('click', function () {
            const rowHtml = '<tr>'
                + '<td><select name="items[' + rowIndex + '][product_id]" class="form-select order-product">' + productSelectHtml('') + '</select></td>'
                + '<td><input type="number" min="1" name="items[' + rowIndex + '][quantity]" class="form-control order-quantity" value="1"></td>'
                + '<td><input type="hidden" name="items[' + rowIndex + '][price]" class="order-price">'
                + moneyInputGroup('order-price-display', 'placeholder="' + priceLabel + '"', '')
                + '</td>'
                + '<td>' + moneyInputGroup('order-subtotal', '', formatMoney(0)) + '</td>'
                + '<td class="text-center"><button type="button" class="btn btn-primary remove-order-item">' + removeLabel + '</button></td>'
                + '</tr>';

            const $row = $(rowHtml);
            $tableBody.append($row);
            bindRow($row);
            rowIndex++;
        });

        if ($providerSelect.is('select')) {
            $providerSelect.on('change', function () {
                selectAddress('', '');
                refreshProviderData();
            });
        }

        refreshProviderData();

        $tableBody.find('tr').each(function () {
            bindRow($(this));
            clearRowServerErrors($(this));
        });
    }

    initOrderFilter();
    initOrderForm();
});
