<?php
/**
 * Branch Single Product Widget
 */

class Branch_Single_Product_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'branch_single_product';
    }

    public function get_title() {
        return esc_html__('Branch Single Product', 'child-hello-elementor');
    }

    public function get_icon() {
        return 'eicon-product-single';
    }

    public function get_categories() {
        return ['branch'];
    }

    public function get_script_depends() {
        if (!wp_script_is('wc-add-to-cart-variation', 'registered')) {
            wp_register_script('wc-add-to-cart-variation', WC()->plugin_url() . '/assets/js/frontend/add-to-cart-variation.min.js', array('jquery', 'wp-util'), WC_VERSION);
        }
        return ['jquery', 'swiper-bundle', 'wc-add-to-cart-variation'];
    }

    public function get_style_depends() {
        return ['swiper-bundle'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'product_section',
            [
                'label' => esc_html__('Product Settings', 'child-hello-elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'use_current_product',
            [
                'label' => esc_html__('Use Current Product', 'child-hello-elementor'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'child-hello-elementor'),
                'label_off' => esc_html__('No', 'child-hello-elementor'),
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'product_id',
            [
                'label' => esc_html__('Select Product', 'child-hello-elementor'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options' => $this->get_products_list(),
                'default' => '',
                'condition' => [
                    'use_current_product' => '',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'style_section',
            [
                'label' => esc_html__('Style', 'child-hello-elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => esc_html__('Title Color', 'child-hello-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .product-title' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'price_color',
            [
                'label' => esc_html__('Price Color', 'child-hello-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .price' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'button_bg_color',
            [
                'label' => esc_html__('Button Background', 'child-hello-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .add-to-cart-button' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();
    }

    private function get_products_list() {
        $products = wc_get_products([
            'status' => 'publish',
            'limit' => -1,
        ]);

        $options = [];
        foreach ($products as $product) {
            $options[$product->get_id()] = $product->get_name();
        }

        return $options;
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        
        if ($settings['use_current_product'] === 'yes' && is_product()) {
            $product_id = get_queried_object_id();
        } else {
            $product_id = $settings['product_id'];
        }

        if (!$product_id) {
            if (is_product()) {
                $product_id = get_queried_object_id();
            } else {
                echo esc_html__('Please select a product or use this widget on a product page.', 'child-hello-elementor');
                return;
            }
        }

        $product = wc_get_product($product_id);
        if (!$product) return;

        $variations = [];
        $variation_attributes = [];
        if ($product->is_type('variable')) {
            $variations = $product->get_available_variations();
            $variation_attributes = $product->get_variation_attributes();
        }
        ?>
        <div class="branch-single-product container w-full md:px-0">
            <div class="flex flex-col md:flex-row gap-8 justify-between">
                <?php $this->render_product_gallery($product); ?>
                <?php $this->render_product_summary($product, $variations); ?>
            </div>
            <?php $this->render_related_products($product); ?>
        </div>

        <style>
            .quantity-controls {
                display: flex;
                flex-direction: column;
            }

            .quantity-controls .quantity {
                border: none!important;
                background: #fef9f3!important;
            }

            .quantity-controls input {
                width: 32px;
                height: 32px;
                text-align: center;
                border: none;
                font-size: 14px;
                padding: 0;
                margin: 0;
                -moz-appearance: textfield;
                background: none!important;
            }

            .quantity-controls input::-webkit-outer-spin-button,
            .quantity-controls input::-webkit-inner-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }

            .quantity-controls button {
                width: 32px;
                height: 32px;
                border: none;
                background: none;
                cursor: pointer;
                font-size: 16px;
                color: #2F2C27;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .quantity-controls button:hover {
                background: #f5f5f5;
                border-radius: 20px;
            }

            .quantity-controls .minus:hover, .quantity-controls .minus:focus, .quantity-controls .plus:hover, .quantity-controls .plus:focus {
                color: black;
            }

            .variation-option {
                position: relative;
                overflow: hidden;
                background: transparent;
                transition: all 0.3s ease;
                border-color: black;
            }

            .variation-option .option-text {
                position: relative;
                z-index: 2;
                transition: color 0.3s ease;
                color: black;
            }

            .variation-option::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: black;
                opacity: 0;
                transition: opacity 0.3s ease;
                z-index: 1;
            }

            .variation-option:hover {
                border-color: black;
            }

            .variation-option:hover::before {
                opacity: 1;
            }

            .variation-option:hover .option-text {
                color: white;
            }

            .variation-option.selected {
                border-color: black;
                background: black;
            }

            .variation-option.selected .option-text {
                color: white;
            }

            .button-wrapper {
                transition: all 0.3s ease;
            }

            .add-to-cart-button {
                background: transparent !important;
                transition: all 0.3s ease;
                border-radius: 100px;
                outline: none;
                font-size: 16px!important;
                color: #2F2C27 !important;
            }

            .add-to-cart-button:hover,
            .add-to-cart-button:focus,
            .add-to-cart-button:active,
            .button-wrapper:hover .add-to-cart-button,
            .button-wrapper:focus-within .add-to-cart-button,
            .button-wrapper:active .add-to-cart-button {
                background: transparent !important;
                color: #2F2C27 !important;
                outline: none;
            }

            .add-to-cart-button:disabled {
                opacity: 0.5;
                cursor: not-allowed;
            }

            .woocommerce-variation-unavailable {
                margin: 1rem 0;
            }

            .woocommerce-variation-unavailable .stock.out-of-stock {
                color: #e2401c;
                font-weight: 500;
                font-size: 0.875rem;
            }

            .single_variation {
                margin-bottom: 1rem;
            }

            @media (max-width: 768px) {
                .view-button-text {
                    font-size: 16px!important;
                }
                .quantity-controls h4 {
                    display: none;
                }
                .quantity-controls {
                    width: fit-content;
                }
                .button-wrapper {
                    width: 180px;
                }
            }
        </style>
        <style>
            .variation-option {
                position: relative;
                overflow: hidden;
                background: transparent;
                transition: all 0.3s ease;
                border-color: #E5E7EB;
                min-width: 80px;
                text-align: center;
            }

            .variation-option .option-text {
                position: relative;
                z-index: 2;
                transition: color 0.3s ease;
                color: #2F2C27;
            }

            .variation-option::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: #2F2C27;
                opacity: 0;
                transition: opacity 0.3s ease;
                z-index: 1;
            }

            .variation-option:hover {
                border-color: #2F2C27;
            }

            .variation-option:hover::before {
                opacity: 0.05;
            }

            .variation-option:hover .option-text {
                color: white;
            }

            .variation-option.selected {
                border-color: #2F2C27;
                background: #2F2C27;
            }

            .variation-option.selected .option-text {
                color: white;
            }

            .variation-option:disabled {
                opacity: 0.5;
                cursor: not-allowed;
                border-color: #E5E7EB;
            }

            .reset_variations {
                display: inline-block;
                margin-top: 0.5rem;
                font-size: 0.875rem;
                color: #2F2C27;
                text-decoration: underline;
                opacity: 0.7;
                transition: opacity 0.3s ease;
            }

            .reset_variations:hover {
                opacity: 1;
            }

            .woocommerce-variation-price {
                margin-bottom: 1rem;
            }

            .woocommerce-variation-availability {
                margin-bottom: 1rem;
                font-size: 0.875rem;
            }

            .stock.out-of-stock {
                color: #EF4444;
            }

            .stock.in-stock {
                color: #10B981;
            }
        </style>
        <style>
            .variations select {
                display: none !important;
                position: absolute;
                visibility: hidden;
                opacity: 0;
                pointer-events: none;
                width: 0;
                height: 0;
                margin: 0;
                padding: 0;
                border: 0;
            }
        </style>
        <style>
            .variation-option.selected {
                border-color: black;
                background-color: #f3f4f6;
            }
            .quantity-wrapper input.product-quantity {
                width: 50px;
                text-align: center;
            }
            .button-wrapper {
                transition: all 0.3s ease;
            }
            .add-to-cart-button {
                background: transparent !important;
                transition: all 0.3s ease;
                border-radius: 100px;
                outline: none;
                font-size: 16px!important;
                color: #2F2C27 !important;
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                height: 100% !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                font-family: 'PP Neue Machina' !important;
                text-transform: uppercase !important;
                letter-spacing: 0.05em !important;
                position: absolute !important;
                inset: 0 !important;
                cursor: pointer !important;
            }
            .add-to-cart-button:hover,
            .add-to-cart-button:focus,
            .add-to-cart-button:active,
            .button-wrapper:hover .add-to-cart-button,
            .button-wrapper:focus-within .add-to-cart-button,
            .button-wrapper:active .add-to-cart-button {
                background: transparent !important;
                color: #2F2C27 !important;
                outline: none !important;
                opacity: 0.9 !important;
            }
            .add-to-cart-button:disabled {
                opacity: 0.5 !important;
                cursor: not-allowed !important;
                background: transparent !important;
            }
            .button-wrapper img {
                width: 100% !important;
                height: auto !important;
                display: block !important;
            }
            @media (max-width: 768px) {
                .view-button-text {
                    font-size: 16px!important;
                }
                .quantity-controls h4 {
                    display: none;
                }
                .quantity-controls {
                    width: fit-content;
                }
                .button-wrapper {
                    width: 180px;
                }
            }
        </style>
        <?php
        if ($product->is_type('variable')) : ?>
            <script>
            jQuery(document).ready(function($) {
                var $form = $('form.variations_form');
                var $addToCartButton = $form.find('.single_add_to_cart_button');
                var $buttonText = $addToCartButton.find('.button-text');
                var $variationButtons = $('.variation-option');
                
                // Initialize variations form
                $form.wc_variation_form();
                
                // Handle variation button clicks
                $variationButtons.on('click', function(e) {
                    e.preventDefault();
                    var $button = $(this);
                    var attribute = $button.data('attribute');
                    var value = $button.data('value');
                    
                    // Update select input
                    var $select = $('#' + attribute);
                    $select.val(value).trigger('change');
                    
                    // Update button states
                    $button.siblings().removeClass('selected');
                    $button.addClass('selected');
                });

                // Handle variation change
                $form.on('found_variation', function(event, variation) {
                    // Enable add to cart button and update text
                    $addToCartButton.prop('disabled', false);
                    $buttonText.text('<?php echo esc_js($product->single_add_to_cart_text()); ?>');
                    
                    // Update variation ID
                    $form.find('input[name="variation_id"]').val(variation.variation_id);

                    // Update price if it exists
                    if (variation.price_html) {
                        $('.price').html(variation.price_html);
                    }

                    // Update stock status
                    if (!variation.is_in_stock) {
                        $addToCartButton.prop('disabled', true);
                        $buttonText.text('<?php esc_html_e('Out of stock', 'woocommerce'); ?>');
                    }
                });

                $form.on('hide_variation', function() {
                    // Disable add to cart button and reset text
                    $addToCartButton.prop('disabled', true);
                    $buttonText.text('<?php esc_html_e('Select options', 'woocommerce'); ?>');
                    $form.find('input[name="variation_id"]').val('');
                });

                // Reset button when clear selection is triggered
                $form.on('reset_data', function() {
                    $variationButtons.removeClass('selected');
                    $addToCartButton.prop('disabled', true);
                    $buttonText.text('<?php esc_html_e('Select options', 'woocommerce'); ?>');
                    $form.find('input[name="variation_id"]').val('');
                    $('select[name^="attribute_"]').val('');
                });

                // Handle form submission
                $form.on('submit', function(e) {
                    var variation_id = $form.find('input[name="variation_id"]').val();
                    if (!variation_id || variation_id === '0') {
                        e.preventDefault();
                        alert('<?php echo esc_js(__('Please select all product options before adding to cart.', 'woocommerce')); ?>');
                        return false;
                    }
                });
            });
            </script>
        <?php endif; ?>
        <?php
        if ($product->is_type('variable')) : ?>
            <script>
            jQuery(document).ready(function($) {
                // Swiper initialization
                const productThumbnailsSwiper = new Swiper('.product-thumbnails', {
                    slidesPerView: 3,
                    spaceBetween: 10,
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev',
                    },
                    watchOverflow: true,
                    slidesOffsetBefore: 0,
                    slidesOffsetAfter: 0,
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
                $('.thumbnail-item').click(function() {
                    var fullImageUrl = $(this).data('full-image');
                    var newImage = $('<img>', {
                        src: fullImageUrl,
                        class: 'w-full h-full object-contain main-product-image'
                    });
                    $('.main-image-container').html(newImage);
                });

                // Quantity controls
                $('.quantity-controls .minus').on('click', function(e) {
                    e.preventDefault();
                    var input = $(this).closest('.quantity-controls').find('.quantity-input');
                    var value = parseInt(input.val());
                    if (value > 1) {
                        input.val(value - 1);
                    }
                });

                $('.quantity-controls .plus').on('click', function(e) {
                    e.preventDefault();
                    var input = $(this).closest('.quantity-controls').find('.quantity-input');
                    var value = parseInt(input.val());
                    input.val(value + 1);
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
                    $('#tab-' + tabId).toggleClass('hidden');
                });
            });
            </script>
        <?php endif; ?>
        <?php
    }

    protected function render_product_gallery($product) {
        ?>
        <div class="w-full md:w-1/2 relative">
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/corner-design.svg" 
                 alt="" 
                 class="absolute -top-16 -left-8 z-10 w-48 pointer-events-none">
            
            <?php
            $attachment_ids = $product->get_gallery_image_ids();
            $featured_image_id = $product->get_image_id();
            
            if ($featured_image_id) {
                echo '<div class="main-image-container" style="max-height: 600px; height: 600px; display: flex; align-items: center; justify-content: center; overflow: hidden;">';
                echo wp_get_attachment_image($featured_image_id, 'full', false, ['class' => 'w-full h-full object-contain main-product-image']);
                echo '</div>';
            }
            
            if ($attachment_ids) {
                echo '<div class="swiper product-thumbnails">';
                echo '<div class="swiper-wrapper">';
                if ($featured_image_id) {
                    $full_image_url = wp_get_attachment_image_url($featured_image_id, 'full');
                    echo '<div class="swiper-slide thumbnail-item" data-full-image="' . esc_attr($full_image_url) . '">';
                    echo wp_get_attachment_image($featured_image_id, 'large', false, ['class' => 'w-full h-full object-cover cursor-pointer']);
                    echo '</div>';
                }
                foreach ($attachment_ids as $attachment_id) {
                    $full_image_url = wp_get_attachment_image_url($attachment_id, 'full');
                    echo '<div class="swiper-slide thumbnail-item" data-full-image="' . esc_attr($full_image_url) . '">';
                    echo wp_get_attachment_image($attachment_id, 'large', false, ['class' => 'w-full h-full object-cover cursor-pointer']);
                    echo '</div>';
                }
                echo '</div>';
                echo '<div class="swiper-button-next !-right-1"></div>';
                echo '<div class="swiper-button-prev !-left-1"></div>';
                echo '</div>';
            }
            ?>
        </div>
        <?php
    }

    protected function render_product_summary($product, $variations) {
        ?>
        <div class="product-summary w-full md:w-1/2 md:px-0">
            <h1 class="product-title text-2xl md:text-4xl font-normal mb-2 uppercase">
                <?php echo esc_html($product->get_name()); ?>
            </h1>
            <div class="price text-2xl md:text-4xl uppercase mb-4">
                <?php echo $product->get_price_html(); ?>
            </div>
            <div class="description text-lg md:text-xl uppercase mb-8">
                <?php echo wp_kses_post($product->get_description()); ?>
            </div>

            <?php $this->render_add_to_cart_form($product); ?>

            <?php 
            $this->render_product_tabs($product);
            echo do_shortcode('[product_accordion]'); 
            ?>
        </div>
        <?php
    }

    protected function render_add_to_cart_form($product) {
        ?>
        <form class="cart variations_form" 
              method="post" 
              enctype="multipart/form-data"
              <?php if ($product->is_type('variable')): ?>
              data-product_id="<?php echo absint($product->get_id()); ?>"
              data-product_variations="<?php echo htmlspecialchars(wp_json_encode($product->get_available_variations())); ?>"
              <?php endif; ?>>

            <?php if ($product->is_type('variable')) : ?>
                <?php $attributes = $product->get_variation_attributes(); ?>
                <div class="variations mb-6">
                    <?php foreach ($attributes as $attribute_name => $options) : ?>
                        <div class="variation-row mb-4">
                            <label for="<?php echo esc_attr(sanitize_title($attribute_name)); ?>" class="text-base uppercase mb-2 block">
                                <?php echo wc_attribute_label($attribute_name); ?>
                            </label>
                            <div class="variation-buttons flex flex-wrap gap-2">
                                <?php
                                if (is_array($options)) {
                                    $selected_value = isset($_REQUEST['attribute_' . sanitize_title($attribute_name)]) 
                                        ? wc_clean(wp_unslash($_REQUEST['attribute_' . sanitize_title($attribute_name)])) 
                                        : $product->get_variation_default_attribute($attribute_name);

                                    if (taxonomy_exists($attribute_name)) {
                                        $terms = wc_get_product_terms($product->get_id(), $attribute_name, array('fields' => 'all'));
                                        foreach ($terms as $term) {
                                            if (!in_array($term->slug, $options)) {
                                                continue;
                                            }
                                            $selected = ($selected_value === $term->slug) ? ' selected' : '';
                                            ?>
                                            <button type="button" 
                                                    class="variation-option px-4 py-2 border rounded-full<?php echo esc_attr($selected); ?>"
                                                    data-attribute="<?php echo esc_attr(sanitize_title($attribute_name)); ?>"
                                                    data-value="<?php echo esc_attr($term->slug); ?>">
                                                <span class="option-text"><?php echo esc_html($term->name); ?></span>
                                            </button>
                                            <?php
                                        }
                                    } else {
                                        foreach ($options as $option) {
                                            $selected = ($selected_value === $option) ? ' selected' : '';
                                            ?>
                                            <button type="button" 
                                                    class="variation-option px-4 py-2 border rounded-full<?php echo esc_attr($selected); ?>"
                                                    data-attribute="<?php echo esc_attr(sanitize_title($attribute_name)); ?>"
                                                    data-value="<?php echo esc_attr($option); ?>">
                                                <span class="option-text"><?php echo esc_html($option); ?></span>
                                            </button>
                                            <?php
                                        }
                                    }
                                }
                                ?>
                                <select id="<?php echo esc_attr(sanitize_title($attribute_name)); ?>"
                                        class="hidden !hidden"
                                        style="display: none !important; visibility: hidden;"
                                        name="attribute_<?php echo esc_attr(sanitize_title($attribute_name)); ?>"
                                        data-attribute_name="attribute_<?php echo esc_attr(sanitize_title($attribute_name)); ?>">
                                    <option value=""><?php echo esc_html__('Choose an option', 'woocommerce'); ?></option>
                                    <?php
                                    if (is_array($options)) {
                                        if (taxonomy_exists($attribute_name)) {
                                            foreach ($terms as $term) {
                                                if (!in_array($term->slug, $options)) {
                                                    continue;
                                                }
                                                echo '<option value="' . esc_attr($term->slug) . '" ' . selected($selected_value, $term->slug, false) . '>' . esc_html($term->name) . '</option>';
                                            }
                                        } else {
                                            foreach ($options as $option) {
                                                echo '<option value="' . esc_attr($option) . '" ' . selected($selected_value, $option, false) . '>' . esc_html($option) . '</option>';
                                            }
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="reset-variations-wrapper mt-2">
                        <a class="reset_variations hidden" href="#"><?php esc_html_e('Clear selection', 'woocommerce'); ?></a>
                    </div>
                </div>

                <div class="single_variation_wrap">
                    <div class="woocommerce-variation single_variation"></div>
                    <div class="woocommerce-variation-add-to-cart variations_button">
                        <?php if ($product->is_in_stock()) : ?>
                            <div class="flex flex-row items-center md:items-end gap-4 md:gap-6">
                                <?php $this->render_quantity_controls(); ?>

                                <div class="flex-1 md:flex-none">
                                    <div class="relative w-full md:w-[180px] cursor-pointer button-wrapper">
                                        <input type="hidden" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>">
                                        <input type="hidden" name="product_id" value="<?php echo esc_attr($product->get_id()); ?>">
                                        <input type="hidden" name="variation_id" class="variation_id" value="0">
                                        
                                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/icons/button-circles.svg" 
                                             alt="button background" 
                                             class="w-full button-bg">
                                        <button type="submit" 
                                                class="add-to-cart-button single_add_to_cart_button button alt absolute inset-0 flex items-center justify-center text-xl md:text-sm text-black font-['PP_Neue_Machina'] uppercase border-none transition-all duration-300"
                                                disabled>
                                            <span class="button-text"><?php esc_html_e('Select options', 'woocommerce'); ?></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php else : ?>
                            <p class="stock out-of-stock">
                                <?php echo esc_html($product->get_stock_status_text()); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <script>
                jQuery(document).ready(function($) {
                    var $form = $('form.variations_form');
                    var $addToCartButton = $form.find('.single_add_to_cart_button');
                    var $buttonText = $addToCartButton.find('.button-text');
                    var $variationButtons = $('.variation-option');
                    
                    // Initialize variations form
                    $form.wc_variation_form();
                    
                    // Handle variation button clicks
                    $variationButtons.on('click', function(e) {
                        e.preventDefault();
                        var $button = $(this);
                        var attribute = $button.data('attribute');
                        var value = $button.data('value');
                        
                        // Update select input
                        var $select = $('#' + attribute);
                        $select.val(value).trigger('change');
                        
                        // Update button states
                        $button.siblings().removeClass('selected');
                        $button.addClass('selected');
                    });

                    // Handle variation change
                    $form.on('found_variation', function(event, variation) {
                        // Enable add to cart button and update text
                        $addToCartButton.prop('disabled', false);
                        $buttonText.text('<?php echo esc_js($product->single_add_to_cart_text()); ?>');
                        
                        // Update variation ID
                        $form.find('input[name="variation_id"]').val(variation.variation_id);

                        // Update price if it exists
                        if (variation.price_html) {
                            $('.price').html(variation.price_html);
                        }

                        // Update stock status
                        if (!variation.is_in_stock) {
                            $addToCartButton.prop('disabled', true);
                            $buttonText.text('<?php esc_html_e('Out of stock', 'woocommerce'); ?>');
                        }
                    });

                    $form.on('hide_variation', function() {
                        // Disable add to cart button and reset text
                        $addToCartButton.prop('disabled', true);
                        $buttonText.text('<?php esc_html_e('Select options', 'woocommerce'); ?>');
                        $form.find('input[name="variation_id"]').val('');
                    });

                    // Reset button when clear selection is triggered
                    $form.on('reset_data', function() {
                        $variationButtons.removeClass('selected');
                        $addToCartButton.prop('disabled', true);
                        $buttonText.text('<?php esc_html_e('Select options', 'woocommerce'); ?>');
                        $form.find('input[name="variation_id"]').val('');
                        $('select[name^="attribute_"]').val('');
                    });

                    // Handle form submission
                    $form.on('submit', function(e) {
                        var variation_id = $form.find('input[name="variation_id"]').val();
                        if (!variation_id || variation_id === '0') {
                            e.preventDefault();
                            alert('<?php echo esc_js(__('Please select all product options before adding to cart.', 'woocommerce')); ?>');
                            return false;
                        }
                    });
                });
                </script>
            <?php else: ?>
                <div class="flex flex-row items-center md:items-end gap-4 md:gap-6">
                    <?php $this->render_quantity_controls(); ?>

                    <div class="flex-1 md:flex-none">
                        <div class="relative w-full md:w-[180px] cursor-pointer button-wrapper">
                            <input type="hidden" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/icons/button-circles.svg" 
                                 alt="button background" 
                                 class="w-full button-bg">
                            <button type="submit" 
                                    class="add-to-cart-button single_add_to_cart_button absolute inset-0 flex items-center justify-center text-xl md:text-sm text-black font-['PP_Neue_Machina'] uppercase border-none transition-all duration-300">
                                <?php echo esc_html($product->single_add_to_cart_text()); ?>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </form>
        <?php
    }

    protected function render_quantity_controls() {
        ?>
        <div class="quantity-controls">
            <h4 class="text-base mb-2">QUANTITY</h4>
            <div class="flex items-center border border-[#E5E7EB] rounded-full w-fit">
                <button type="button" class="minus px-4 py-2 text-lg font-semibold hover:bg-gray-100 rounded-l-full"
                        style="background: none; border-right: 1px solid #E5E7EB;">
                    -
                </button>
                <div class="quantity">
                    <input type="number" 
                           name="quantity"
                           class="product-quantity quantity-input w-12 text-center border-none focus:outline-none" 
                           value="1" 
                           min="1"
                           style="-moz-appearance: textfield;">
                </div>
                <button type="button" class="plus px-4 py-2 text-lg font-semibold hover:bg-gray-100 rounded-r-full"
                        style="background: none; border-left: 1px solid #E5E7EB;">
                    +
                </button>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            // Quantity controls
            $('.quantity-controls .minus').on('click', function(e) {
                e.preventDefault();
                var input = $(this).closest('.quantity-controls').find('.product-quantity');
                var value = parseInt(input.val());
                if (value > 1) {
                    input.val(value - 1);
                }
            });

            $('.quantity-controls .plus').on('click', function(e) {
                e.preventDefault();
                var input = $(this).closest('.quantity-controls').find('.product-quantity');
                var value = parseInt(input.val());
                input.val(value + 1);
            });

            $('.product-quantity').on('change', function() {
                var value = parseInt($(this).val());
                if (value < 1 || isNaN(value)) {
                    $(this).val(1);
                }
            });
        });
        </script>

        <style>
            .quantity-controls {
                display: flex;
                flex-direction: column;
            }

            .quantity-controls .quantity {
                border: none!important;
                background: #fef9f3!important;
            }

            .quantity-controls input {
                width: 32px;
                height: 32px;
                text-align: center;
                border: none;
                font-size: 14px;
                padding: 0;
                margin: 0;
                -moz-appearance: textfield;
                background: none!important;
            }

            .quantity-controls input::-webkit-outer-spin-button,
            .quantity-controls input::-webkit-inner-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }

            .quantity-controls button {
                width: 32px;
                height: 32px;
                border: none;
                background: none;
                cursor: pointer;
                font-size: 16px;
                color: #2F2C27;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .quantity-controls button:hover {
                background: #f5f5f5;
                border-radius: 20px;
            }

            .quantity-controls .minus:hover,
            .quantity-controls .minus:focus,
            .quantity-controls .plus:hover,
            .quantity-controls .plus:focus {
                color: black;
            }

            @media (max-width: 768px) {
                .quantity-controls h4 {
                    display: none;
                }
                .quantity-controls {
                    width: fit-content;
                }
            }
        </style>
        <?php
    }

    protected function render_product_tabs($product) {
        ?>
        <div class="product-tabs mt-8 border-t border-gray-200">
            <?php
            $tabs = [
                'materials' => __('Materials', 'child-hello-elementor'),
                'dimensions' => __('Dimensions', 'child-hello-elementor'),
                'care' => __('Care instructions', 'child-hello-elementor'),
            ];

            foreach ($tabs as $key => $label):
                $content = $product->get_attribute($key);
                if (!$content) continue;
                ?>
                <div class="py-4 border-t border-gray-200">
                    <button class="flex items-center justify-between w-full text-left" data-tab="<?php echo esc_attr($key); ?>">
                        <span class="text-sm font-medium"><?php echo esc_html($label); ?></span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="tab-content hidden mt-4" id="tab-<?php echo esc_attr($key); ?>">
                        <?php echo wp_kses_post($content); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }

    protected function render_related_products($product) {
        $related_products = wc_get_related_products($product->get_id(), 2);
        if (empty($related_products)) return;
        ?>
        <div class="related-products-section container w-full mt-12 md:px-0">
            <div class="flex flex-col md:flex-row gap-8 md:gap-12 py-8 md:py-12">
                <div class="w-full md:w-1/2">
                    <h3 class="text-[#2F2C27] font-['PP_Neue_Machina'] text-4xl md:text-7xl font-normal leading-tight md:leading-[74.6px] uppercase">
                        <?php esc_html_e('Discover Your Other Favorite Hoodie – Limited Edition', 'child-hello-elementor'); ?>
                    </h3>
                </div>

                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <?php foreach ($related_products as $related_product_id):
                        $related_product = wc_get_product($related_product_id);
                        if (!$related_product) continue;
                        
                        $image = wp_get_attachment_image_src(get_post_thumbnail_id($related_product_id), 'full');
                        if (!$image) continue;
                        ?>
                        <div class="flex flex-col gap-4 md:gap-6">
                            <a href="<?php echo esc_url($related_product->get_permalink()); ?>">
                                <img class="w-full h-auto" 
                                     src="<?php echo esc_url($image[0]); ?>" 
                                     alt="<?php echo esc_attr($related_product->get_name()); ?>">
                            </a>

                            <h3 class="text-[#2F2C27] text-2xl md:text-[36px] font-normal uppercase">
                                <a href="<?php echo esc_url($related_product->get_permalink()); ?>" 
                                   class="text-[#2F2C27] font-['PP_Neue_Machina'] text-2xl md:text-[36px] font-normal uppercase">
                                    <?php echo esc_html($related_product->get_name()); ?>
                                </a>
                            </h3>
                            <p class="text-[#2F2C27] font-['PP_Neue_Machina'] text-lg md:text-[24px] font-normal uppercase">
                                <?php echo wp_kses_post($related_product->get_short_description()); ?>
                            </p>
                            <span class="text-[#2F2C27] font-['PP_Neue_Machina'] text-2xl md:text-3xl font-normal uppercase">
                                <?php echo esc_html($related_product->get_price()); ?>$
                            </span>
                            <div class="flex items-center gap-4 justify-end">
                                <a href="<?php echo esc_url($related_product->get_permalink()); ?>" 
                                   class="relative w-fit cursor-pointer view-button">
                                    <img src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/icons/button-circles.svg" 
                                         alt="button background" 
                                         class="w-56">
                                    <span class="view-button-text absolute inset-0 flex items-center justify-center text-xl text-black uppercase">
                                        <?php esc_html_e('VIEW', 'child-hello-elementor'); ?>
                                    </span>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php
    }
}