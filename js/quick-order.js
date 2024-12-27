jQuery(document).ready(function($) {
    var $form = $('form.variations_form');
    var $addToCartButton = $('.single_add_to_cart_button');
    
    // Variation selection handling
    $('.variation-option').on('click', function() {
        var $this = $(this);
        var attribute = $this.data('attribute');
        var value = $this.data('value');

        // Update visual selection
        $this.closest('.variation-select').find('.variation-option').removeClass('selected');
        $this.addClass('selected');

        // Update hidden input
        var inputName = 'attribute_' + attribute;
        $('input[name="' + inputName + '"]').val(value).trigger('change');

        // Check if all variations are selected
        var allSelected = true;
        $('.variation-select-input').each(function() {
            if (!$(this).val()) {
                allSelected = false;
                return false;
            }
        });

        // Enable/disable add to cart button
        $addToCartButton.prop('disabled', !allSelected);
        if (allSelected) {
            $addToCartButton.removeClass('disabled');
        } else {
            $addToCartButton.addClass('disabled');
        }
    });

    // Quantity controls
    $('.quantity-controls .minus').on('click', function(e) {
        e.preventDefault();
        var $input = $(this).closest('.quantity-controls').find('.quantity-input');
        var value = parseInt($input.val());
        if (value > 1) {
            $input.val(value - 1).trigger('change');
        }
    });

    $('.quantity-controls .plus').on('click', function(e) {
        e.preventDefault();
        var $input = $(this).closest('.quantity-controls').find('.quantity-input');
        var value = parseInt($input.val());
        $input.val(value + 1).trigger('change');
    });

    $('.quantity-input').on('change', function() {
        var value = parseInt($(this).val());
        if (value < 1 || isNaN(value)) {
            $(this).val(1);
        }
    });

    // Product tabs
    $('.product-tabs button').on('click', function() {
        var tabId = $(this).data('tab');
        $('#tab-' + tabId).slideToggle();
        $(this).find('svg').toggleClass('rotate-180');
    });
});
