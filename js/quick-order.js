jQuery(document).ready(function($) {
    // Only initialize if we're on the quick order page
    if (!$('.quick-order-form').length) return;

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
    $('.quick-order-tabs button').on('click', function() {
        var tabId = $(this).data('tab');
        $('#tab-' + tabId).slideToggle();
        $(this).find('svg').toggleClass('rotate-180');
    });
});
