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
        return ['jquery', 'swiper-bundle', 'wc-add-to-cart-variation', 'branch-product-scripts'];
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

            .quantity-controls .minus:hover, .minus:focus, .plus:hover, .plus:focus {
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
    }

    protected function render_product_gallery($product) {
        $featured_image_id = $product->get_image_id();
        $gallery_image_ids = $product->get_gallery_image_ids();
        ?>
        <div class="w-full md:w-1/2 relative">
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/corner-design.svg" 
                 alt="" 
                 class="absolute -top-16 -left-8 z-10 w-48 pointer-events-none">
            
            <?php if ($featured_image_id): ?>
                <div class="main-image-container" style="max-height: 600px; height: 600px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                    <?php 
                    echo wp_get_attachment_image(
                        $featured_image_id, 
                        'full', 
                        false, 
                        ['class' => 'w-full h-full object-contain main-product-image']
                    ); 
                    ?>
                </div>
            <?php endif; ?>

            <?php if ($gallery_image_ids): ?>
                <div class="swiper product-thumbnails">
                    <div class="swiper-wrapper">
                        <?php
                        if ($featured_image_id) {
                            $this->render_thumbnail_slide($featured_image_id);
                        }
                        foreach ($gallery_image_ids as $attachment_id) {
                            $this->render_thumbnail_slide($attachment_id);
                        }
                        ?>
                    </div>
                    <div class="swiper-button-next !-right-1"></div>
                    <div class="swiper-button-prev !-left-1"></div>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }

    protected function render_thumbnail_slide($attachment_id) {
        $full_image_url = wp_get_attachment_image_url($attachment_id, 'full');
        ?>
        <div class="swiper-slide thumbnail-item" data-full-image="<?php echo esc_attr($full_image_url); ?>">
            <?php 
            echo wp_get_attachment_image(
                $attachment_id, 
                'large', 
                false, 
                ['class' => 'w-full h-full object-cover cursor-pointer']
            ); 
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

    protected function get_variations_data($product) {
        if (!$product->is_type('variable')) {
            return [];
        }

        $variations = $product->get_available_variations();
        $attributes = $product->get_variation_attributes();
        
        $processed_variations = [];
        foreach ($variations as $variation) {
            $processed_attributes = [];
            foreach ($variation['attributes'] as $key => $value) {
                $key = str_replace('attribute_', '', $key);
                $processed_attributes[$key] = $value;
            }
            $variation['attributes'] = $processed_attributes;
            $processed_variations[] = $variation;
        }

        return [
            'variations' => $processed_variations,
            'attributes' => $attributes
        ];
    }

    protected function render_add_to_cart_form($product) {
        $variations_data = $this->get_variations_data($product);
        ?>
        <form class="variations_form cart" method="post" enctype="multipart/form-data" data-product_id="<?php echo esc_attr($product->get_id()); ?>">
            <?php if ($product->is_type('variable')) : ?>
                <div class="variations">
                    <?php foreach ($product->get_variation_attributes() as $attribute_name => $options) : ?>
                        <div class="variation-row">
                            <div class="label">
                                <label for="<?php echo esc_attr(sanitize_title($attribute_name)); ?>">
                                    <?php echo wc_attribute_label($attribute_name); ?>
                                </label>
                            </div>
                            <div class="value">
                                <div class="variation-select">
                                    <?php
                                    $selected = isset($_REQUEST['attribute_' . sanitize_title($attribute_name)]) 
                                        ? wc_clean(wp_unslash($_REQUEST['attribute_' . sanitize_title($attribute_name)])) 
                                        : $product->get_variation_default_attribute($attribute_name);
                                    
                                    foreach ($options as $option) : 
                                        $selected_class = ($selected === $option) ? ' selected' : '';
                                    ?>
                                        <button type="button" 
                                                class="variation-option<?php echo esc_attr($selected_class); ?>" 
                                                data-attribute="<?php echo esc_attr(sanitize_title($attribute_name)); ?>" 
                                                data-value="<?php echo esc_attr($option); ?>">
                                            <span class="option-text"><?php echo esc_html($option); ?></span>
                                        </button>
                                    <?php endforeach; ?>
                                    <input type="hidden" 
                                           name="attribute_<?php echo esc_attr(sanitize_title($attribute_name)); ?>" 
                                           class="variation-select-input" 
                                           value="<?php echo esc_attr($selected); ?>">
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" name="variation_id" class="variation_id" value="">
            <?php endif; ?>

            <div class="single_variation_wrap">
                <div class="woocommerce-variation single_variation"></div>
                <div class="woocommerce-variation-add-to-cart variations_button">
                    <?php if ($product->is_in_stock()) : ?>
                        <div class="flex flex-row items-center md:items-end gap-4 md:gap-6">
                            <!-- Quantity Controls -->
                            <div class="quantity-wrapper">
                                <h4 class="text-base mb-2">QUANTITY</h4>
                                <div class="quantity">
                                    <button type="button" class="minus">-</button>
                                    <input type="number" 
                                           name="quantity"
                                           class="product-quantity" 
                                           value="1" 
                                           min="1">
                                    <button type="button" class="plus">+</button>
                                </div>
                            </div>

                            <!-- Add to Cart Button -->
                            <div class="flex-1 md:flex-none">
                                <div class="relative w-full md:w-[180px] cursor-pointer button-wrapper">
                                    <input type="hidden" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>">
                                    <?php if ($product->is_type('variable')): ?>
                                        <input type="hidden" name="product_id" value="<?php echo esc_attr($product->get_id()); ?>">
                                    <?php endif; ?>
                                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/icons/button-circles.svg" 
                                         alt="button background" 
                                         class="w-full button-bg">
                                    <button type="submit" 
                                            class="add-to-cart-button single_add_to_cart_button absolute inset-0 flex items-center justify-center text-xl md:text-sm text-black font-['PP_Neue_Machina'] uppercase border-none transition-all duration-300 <?php echo $product->is_type('variable') ? 'disabled' : ''; ?>"
                                            <?php echo $product->is_type('variable') ? 'disabled' : ''; ?>>
                                        <?php echo esc_html($product->single_add_to_cart_text()); ?>
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
        </form>

        <script type="text/javascript">
            var productVariations = <?php echo wp_json_encode($variations_data['variations']); ?>;
        </script>
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