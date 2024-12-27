(function($) {
    'use strict';

    class BranchProductHandler {
        constructor() {
            this.form = $('form.variations_form');
            this.addToCartButton = $('.single_add_to_cart_button');
            this.variationData = this.form.data('product_variations');
            
            this.initializeSwiper();
            this.initializeQuantityControls();
            this.initializeVariationHandling();
            this.initializeAccordion();
            this.initializeAjaxAddToCart();
        }

        initializeSwiper() {
            new Swiper('.product-thumbnails', {
                slidesPerView: 3,
                spaceBetween: 10,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                watchOverflow: true,
                breakpoints: {
                    320: {
                        slidesPerView: 3,
                        spaceBetween: 10
                    },
                    768: {
                        slidesPerView: 3,
                        spaceBetween: 10
                    }
                }
            });

            // Thumbnail click handler
            $('.thumbnail-item').on('click', function() {
                const fullImageUrl = $(this).data('full-image');
                const newImage = $('<img>', {
                    src: fullImageUrl,
                    class: 'w-full h-full object-contain main-product-image'
                });
                $('.main-image-container').html(newImage);
            });
        }

        initializeQuantityControls() {
            $('.minus').on('click', function(e) {
                e.preventDefault();
                const input = $(this).closest('.quantity').find('input.product-quantity');
                const value = parseInt(input.val());
                if (value > 1) {
                    input.val(value - 1).trigger('change');
                }
            });

            $('.plus').on('click', function(e) {
                e.preventDefault();
                const input = $(this).closest('.quantity').find('input.product-quantity');
                const value = parseInt(input.val());
                input.val(value + 1).trigger('change');
            });

            $('input.product-quantity').on('change', function() {
                let value = parseInt($(this).val());
                if (value < 1 || isNaN(value)) {
                    $(this).val(1);
                }
            });
        }

        initializeVariationHandling() {
            if (!this.form.length) return;

            // Initialize variation form
            this.form.on('found_variation', (event, variation) => {
                this.addToCartButton.prop('disabled', false).removeClass('disabled');
                $('.variation_id').val(variation.variation_id);
            });

            this.form.on('reset_data', () => {
                this.addToCartButton.prop('disabled', true).addClass('disabled');
                $('.variation_id').val('');
            });

            // Handle variation selection
            $('.variation-option').on('click', (e) => {
                e.preventDefault();
                const $this = $(e.currentTarget);
                const attribute = $this.data('attribute');
                const value = $this.data('value');

                // Update visual selection
                $this.closest('.variation-select').find('.variation-option').removeClass('selected');
                $this.addClass('selected');

                // Update hidden input
                const inputName = `attribute_${attribute}`;
                $(`input[name="${inputName}"]`).val(value);

                // Get all selected attributes
                const selectedAttributes = {};
                $('.variation-select-input').each(function() {
                    const name = $(this).attr('name');
                    const val = $(this).val();
                    if (val) {
                        selectedAttributes[name] = val;
                    }
                });

                // Find matching variation
                const matchedVariation = this.findMatchingVariation(selectedAttributes);
                if (matchedVariation) {
                    // Update form
                    $('.variation_id').val(matchedVariation.variation_id);
                    this.addToCartButton.prop('disabled', false).removeClass('disabled');

                    // Trigger WooCommerce variation found event
                    this.form.trigger('found_variation', [matchedVariation]);
                } else {
                    // Reset form if no match found
                    $('.variation_id').val('');
                    this.addToCartButton.prop('disabled', true).addClass('disabled');
                    this.form.trigger('reset_data');
                }
            });
        }

        findMatchingVariation(selectedAttributes) {
            if (!this.variationData) return null;

            return this.variationData.find(variation => {
                return Object.entries(selectedAttributes).every(([name, value]) => {
                    const attributeName = name.replace('attribute_', '');
                    return !variation.attributes[attributeName] || 
                           variation.attributes[attributeName] === value;
                });
            });
        }

        initializeAccordion() {
            $('.product-tabs button').on('click', function() {
                const tabId = $(this).data('tab');
                $(`#tab-${tabId}`).slideToggle();
                $(this).find('svg').toggleClass('rotate-180');
            });
        }

        initializeAjaxAddToCart() {
            this.form.on('submit', (e) => {
                e.preventDefault();
                
                if (this.addToCartButton.hasClass('disabled')) {
                    return false;
                }

                const formData = new FormData(this.form[0]);
                formData.append('action', 'woocommerce_ajax_add_to_cart');

                $.ajax({
                    type: 'POST',
                    url: wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'add_to_cart'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: (response) => {
                        if (!response.error) {
                            $(document.body).trigger('added_to_cart', [
                                response.fragments,
                                response.cart_hash,
                                this.addToCartButton
                            ]);
                        } else {
                            window.alert(response.message || 'Error adding to cart');
                        }
                    },
                    error: () => {
                        window.alert('Error occurred while adding to cart. Please try again.');
                    }
                });

                return false;
            });
        }
    }

    // Initialize on document ready
    $(document).ready(() => new BranchProductHandler());

})(jQuery);
