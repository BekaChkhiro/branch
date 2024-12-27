(function($) {
    'use strict';

    class BranchProductHandler {
        constructor() {
            this.form = $('.variations_form');
            this.addToCartButton = $('.single_add_to_cart_button');
            this.variationContainer = $('.single_variation');
            this.mainImage = $('.main-product-image');
            
            this.initializeVariationHandling();
            this.initializeQuantityControls();
            this.initializeAccordion();
        }

        initializeVariationHandling() {
            if (!this.form.length) return;

            // Initialize WooCommerce variation form
            this.form.wc_variation_form();

            // Handle our custom variation buttons
            this.form.on('click', '.variation-option', (e) => {
                e.preventDefault();
                const $target = $(e.currentTarget);
                const attribute = $target.data('attribute');
                const value = $target.data('value');

                // Update visual selection
                $target.closest('.variation-row').find('.variation-option').removeClass('selected');
                $target.addClass('selected');

                // Update hidden input and trigger WooCommerce's change event
                $(`input[name="attribute_${attribute}"]`).val(value).trigger('change');
            });

            // Listen to WooCommerce's variation events
            this.form.on('found_variation', (event, variation) => {
                this.updateVariationDisplay(variation);
            });

            this.form.on('reset_data', () => {
                this.resetVariationDisplay();
            });

            this.form.on('hide_variation', () => {
                this.hideVariation();
            });

            this.form.on('show_variation', (event, variation) => {
                this.showVariation(variation);
            });
        }

        updateVariationDisplay(variation) {
            if (!variation) return;

            // Update price
            if (variation.display_price !== undefined) {
                this.variationContainer.find('.woocommerce-variation-price').html(variation.price_html);
            }

            // Update availability
            if (variation.is_in_stock && variation.is_purchasable) {
                this.addToCartButton
                    .prop('disabled', false)
                    .removeClass('disabled')
                    .text('Add to Cart');
            } else {
                this.addToCartButton
                    .prop('disabled', true)
                    .addClass('disabled')
                    .text('Unavailable');
            }

            // Update image if available
            if (variation.image && variation.image.full_src) {
                this.mainImage.attr('src', variation.image.full_src);
            }
        }

        resetVariationDisplay() {
            this.addToCartButton
                .prop('disabled', true)
                .addClass('disabled')
                .text('Select Options');
            
            this.variationContainer.empty();
        }

        hideVariation() {
            this.variationContainer.hide();
            this.addToCartButton
                .prop('disabled', true)
                .addClass('disabled');
        }

        showVariation(variation) {
            this.variationContainer.show();
            this.updateVariationDisplay(variation);
        }

        initializeQuantityControls() {
            const quantityWrapper = $('.quantity-wrapper');
            const quantityInput = quantityWrapper.find('.quantity-input');
            const minusBtn = quantityWrapper.find('.quantity-minus');
            const plusBtn = quantityWrapper.find('.quantity-plus');
            
            minusBtn.on('click', () => {
                let value = parseInt(quantityInput.val()) || 1;
                value = Math.max(1, value - 1);
                quantityInput.val(value).trigger('change');
            });
            
            plusBtn.on('click', () => {
                let value = parseInt(quantityInput.val()) || 1;
                value++;
                quantityInput.val(value).trigger('change');
            });
            
            quantityInput.on('change', function() {
                let value = parseInt($(this).val()) || 1;
                value = Math.max(1, value);
                $(this).val(value);
            });
        }

        initializeAccordion() {
            $('.product-tabs button').on('click', function() {
                const tabId = $(this).data('tab');
                $(`#${tabId}`).slideToggle();
                $(this).find('svg').toggleClass('rotate-180');
            });
        }
    }

    // Initialize on document ready
    $(document).ready(() => new BranchProductHandler());

})(jQuery);
