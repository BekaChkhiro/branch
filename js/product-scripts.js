(function($) {
    'use strict';

    class BranchProductHandler {
        constructor() {
            this.form = $('form.variations_form');
            this.addToCartButton = $('.single_add_to_cart_button');
            this.variationData = typeof productVariations !== 'undefined' ? productVariations : [];
            this.selectedAttributes = {};
            
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
                $(`input[name="${inputName}"]`).val(value).trigger('change');

                // Update selected attributes
                this.selectedAttributes[attribute] = value;

                // Check if all attributes are selected
                const allAttributesSelected = this.checkAllAttributesSelected();

                if (allAttributesSelected) {
                    // Find matching variation
                    const matchedVariation = this.findMatchingVariation();
                    if (matchedVariation) {
                        this.updateVariationInfo(matchedVariation);
                    } else {
                        this.showUnavailableMessage();
                    }
                } else {
                    this.resetVariationForm();
                }
            });

            // Initialize default selections
            this.initializeDefaultAttributes();
        }

        checkAllAttributesSelected() {
            if (!this.variationData.attributes) return false;
            
            return Object.keys(this.variationData.attributes).every(
                attribute => this.selectedAttributes[attribute]
            );
        }

        updateVariationInfo(variation) {
            // Update price
            if (variation.display_price !== undefined) {
                const priceHtml = this.formatPrice(variation.display_price);
                $('.single_variation').html(`<div class="woocommerce-variation-price">${priceHtml}</div>`);
            }

            // Update stock status
            if (variation.is_in_stock) {
                this.addToCartButton.prop('disabled', false).removeClass('disabled');
                $('.single_variation').find('.stock').remove();
            } else {
                this.addToCartButton.prop('disabled', true).addClass('disabled');
                $('.single_variation').append('<p class="stock out-of-stock">Out of stock</p>');
            }

            // Update variation ID
            $('.variation_id').val(variation.variation_id);

            // Update product image if available
            if (variation.image && variation.image.full_src) {
                $('.main-product-image').attr('src', variation.image.full_src);
            }

            // Clear any previous error messages
            $('.single_variation').find('.woocommerce-variation-unavailable').remove();

            // Trigger WooCommerce variation found event
            this.form.trigger('found_variation', [variation]);
        }

        showUnavailableMessage() {
            this.resetVariationForm();
            $('.single_variation').html(
                '<div class="woocommerce-variation-unavailable">' +
                '<p class="stock out-of-stock">Sorry, this product is unavailable. Please choose a different combination.</p>' +
                '</div>'
            );
        }

        resetVariationForm() {
            $('.variation_id').val('');
            this.addToCartButton.prop('disabled', true).addClass('disabled');
            $('.single_variation').find('.woocommerce-variation-price, .stock').remove();
        }

        findMatchingVariation() {
            if (!this.variationData.variations) return null;

            return this.variationData.variations.find(variation => {
                return Object.entries(variation.attributes).every(([name, value]) => {
                    const selectedValue = this.selectedAttributes[name];
                    // Check if the variation attribute is not empty and matches the selected value
                    return !value || value === selectedValue;
                });
            });
        }

        initializeDefaultAttributes() {
            if (!this.variationData.attributes) return;

            Object.entries(this.variationData.attributes).forEach(([attribute, data]) => {
                if (data.default) {
                    const $option = $(`.variation-option[data-attribute="${attribute}"][data-value="${data.default}"]`);
                    if ($option.length) {
                        $option.trigger('click');
                    }
                }
            });
        }

        formatPrice(price) {
            return `<span class="price"><span class="woocommerce-Price-amount amount">
                <bdi><span class="woocommerce-Price-currencySymbol">$</span>${price.toFixed(2)}</bdi></span></span>`;
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
