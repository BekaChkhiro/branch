<?php
function hello_elementor_child_enqueue_styles() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    
    // Tailwind CSS CDN - მარტივი ვერსია
    wp_enqueue_style('tailwind', 
        'https://cdn.jsdelivr.net/npm/tailwindcss@2/dist/tailwind.min.css',
        array(),
        '2.2.19'
    );
    
    // Swiper JS
    wp_enqueue_style('swiper-bundle', 
        'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
        array(),
        '11.0.5'
    );
    wp_enqueue_script('swiper-bundle',
        'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
        array('jquery'),
        '11.0.5',
        true
    );
    
    // Custom scripts for product functionality
    wp_enqueue_script('branch-product-scripts',
        get_stylesheet_directory_uri() . '/js/product-scripts.js',
        array('jquery', 'swiper-bundle', 'wc-add-to-cart-variation'),
        wp_get_theme()->get('Version'),
        true
    );
    
    wp_enqueue_style('child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('parent-style', 'tailwind', 'swiper-bundle'),
        wp_get_theme()->get('Version')
    );

    // Tailwind კონფიგურაცია
    wp_add_inline_script('tailwind', "
        tailwind.config = {
            theme: {
                extend: {
                    // აქ შეგიძლიათ დაამატოთ custom კონფიგურაცია
                }
            }
        }
    ");
}
add_action('wp_enqueue_scripts', 'hello_elementor_child_enqueue_styles', 20);

// ვიჯეტების ფოლდერის მისამართის განსაზღვრა
define('WIDGET_DIR', get_stylesheet_directory() . '/widgets/');

// ვიჯეტების რეგისტრაცია
function register_custom_widgets() {
    // ვიჯეტების ფაილების ავტომატური ჩატვირთვა
    foreach (glob(WIDGET_DIR . '*.php') as $file) {
        require_once $file;
    }
    
    // აქ დაარეგისტრირეთ თქვენი ვიჯეტები
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \Branch_Hero_Section());
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \Branch_Header());
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \Branch_Story_Grid());
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \Branch_Product_Showcase());
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \Branch_Single_Product_Widget());
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \Branch_Footer());
    // დაამატეთ სხვა ვიჯეტები აქ
}

add_action('elementor/widgets/widgets_registered', 'register_custom_widgets');

// Branch კატეგორიის დამატება
function add_elementor_widget_categories($elements_manager) {
    $elements_manager->add_category(
        'branch',
        [
            'title' => 'Branch',
            'icon' => 'fa fa-plug',
        ]
    );
}
add_action('elementor/elements/categories_registered', 'add_elementor_widget_categories');

// Remove WooCommerce Breadcrumb
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);

// Enable WooCommerce debugging
add_action('init', function() {
    if (!defined('WC_DEBUG')) {
        define('WC_DEBUG', true);
    }
});

// Ensure WooCommerce scripts are loaded
function branch_enqueue_wc_scripts() {
    error_log('Checking WooCommerce scripts loading...'); // Debugging
    
    if (function_exists('is_product') && is_product()) {
        error_log('Loading WooCommerce scripts for product page'); // Debugging
        
        // Remove default WooCommerce scripts to prevent conflicts
        wp_dequeue_script('wc-add-to-cart');
        wp_dequeue_script('wc-add-to-cart-variation');
        
        // Re-enqueue in correct order with dependencies
        wp_enqueue_script('jquery');
        wp_enqueue_script('wc-add-to-cart', WC()->plugin_url() . '/assets/js/frontend/add-to-cart.min.js', array('jquery'), WC_VERSION);
        wp_enqueue_script('wc-cart-fragments', WC()->plugin_url() . '/assets/js/frontend/cart-fragments.min.js', array('jquery', 'wc-add-to-cart'), WC_VERSION);
        wp_enqueue_script('wc-add-to-cart-variation', WC()->plugin_url() . '/assets/js/frontend/add-to-cart-variation.min.js', array('jquery', 'wc-add-to-cart'), WC_VERSION);
        
        // Localize the script with new data
        $params = array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'wc_ajax_url' => WC_AJAX::get_endpoint('%%endpoint%%'),
            'i18n_view_cart' => esc_attr__('View cart', 'woocommerce'),
            'cart_url' => wc_get_cart_url(),
            'is_cart' => is_cart(),
            'cart_redirect_after_add' => get_option('woocommerce_cart_redirect_after_add')
        );
        
        wp_localize_script('wc-add-to-cart', 'wc_add_to_cart_params', $params);
        wp_localize_script('wc-add-to-cart-variation', 'wc_add_to_cart_variation_params', array(
            'i18n_no_matching_variations_text' => esc_attr__('Sorry, no products matched your selection. Please choose a different combination.', 'woocommerce'),
            'i18n_make_a_selection_text' => esc_attr__('Please select some product options before adding this product to your cart.', 'woocommerce'),
            'i18n_unavailable_text' => esc_attr__('Sorry, this product is unavailable. Please choose a different combination.', 'woocommerce')
        ));
    }
}
add_action('wp_enqueue_scripts', 'branch_enqueue_wc_scripts', 99);

// AJAX add to cart handler
function branch_ajax_add_to_cart() {
    ob_start();

    try {
        // Check nonce
        if (!isset($_POST['add-to-cart-nonce']) || !wp_verify_nonce($_POST['add-to-cart-nonce'], 'add-to-cart')) {
            throw new Exception('Security check failed');
        }

        // Get product ID
        $product_id = isset($_POST['add-to-cart']) ? absint($_POST['add-to-cart']) : 0;
        if (!$product_id) {
            throw new Exception('Invalid product ID');
        }

        // Get product
        $product = wc_get_product($product_id);
        if (!$product) {
            throw new Exception('Invalid product');
        }

        // Debug info
        error_log('Processing add to cart request:');
        error_log('Product ID: ' . $product_id);
        error_log('POST data: ' . print_r($_POST, true));

        $quantity = empty($_POST['quantity']) ? 1 : wc_stock_amount($_POST['quantity']);
        $variation_id = empty($_POST['variation_id']) ? 0 : absint($_POST['variation_id']);
        
        // Get variation attributes
        $variation = array();
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'attribute_') === 0) {
                $variation[$key] = wc_clean($value);
            }
        }

        error_log('Variation data:');
        error_log('Variation ID: ' . $variation_id);
        error_log('Variations: ' . print_r($variation, true));

        // Validate the product
        if ($product->is_type('variable')) {
            if (!$variation_id) {
                throw new Exception('Please select product options before adding this product to your cart.');
            }

            // Get variation object
            $variation_obj = wc_get_product($variation_id);
            if (!$variation_obj) {
                throw new Exception('Invalid variation ID');
            }

            // Check if all required attributes are set
            $missing_attributes = array();
            foreach ($product->get_variation_attributes() as $attribute => $options) {
                $attribute_key = 'attribute_' . sanitize_title($attribute);
                if (!empty($options) && !isset($variation[$attribute_key])) {
                    $missing_attributes[] = wc_attribute_label($attribute);
                }
            }

            if (!empty($missing_attributes)) {
                throw new Exception('Please select ' . implode(', ', $missing_attributes));
            }
        }

        // Check stock status
        if (!$product->is_in_stock()) {
            throw new Exception('Sorry, this product is out of stock.');
        }

        // Pass validation
        $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variation);
        
        if (!$passed_validation) {
            throw new Exception('Product validation failed');
        }

        // Add to cart
        $cart_item_key = WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variation);
        
        if (!$cart_item_key) {
            throw new Exception('Failed to add product to cart');
        }

        do_action('woocommerce_ajax_added_to_cart', $product_id);

        if (get_option('woocommerce_cart_redirect_after_add') === 'yes') {
            wc_add_to_cart_message(array($product_id => $quantity), true);
        }

        WC_AJAX::get_refreshed_fragments();

    } catch (Exception $e) {
        wp_send_json_error(array(
            'error' => true,
            'message' => $e->getMessage(),
            'product_url' => apply_filters('woocommerce_cart_redirect_after_error', get_permalink($product_id), $product_id)
        ));
    }

    wp_die();
}
add_action('wp_ajax_woocommerce_ajax_add_to_cart', 'branch_ajax_add_to_cart');
add_action('wp_ajax_nopriv_woocommerce_ajax_add_to_cart', 'branch_ajax_add_to_cart');
add_action('wc_ajax_add_to_cart', 'branch_ajax_add_to_cart');

// Add debug info to footer
add_action('wp_footer', function() {
    if (is_product()) {
        ?>
        <script>
        console.log('WooCommerce Debug Info:');
        console.log('WC AJAX URL:', typeof wc_add_to_cart_params !== 'undefined' ? wc_add_to_cart_params.ajax_url : 'Not defined');
        console.log('jQuery version:', jQuery.fn.jquery);
        console.log('WooCommerce scripts loaded:', {
            'wc-add-to-cart': typeof wc_add_to_cart_params !== 'undefined',
            'wc-add-to-cart-variation': typeof wc_add_to_cart_variation_params !== 'undefined',
            'wc-cart-fragments': typeof wc_cart_fragments_params !== 'undefined'
        });
        </script>
        <?php
    }
});

/* product accordion */

// Register accordion meta box
function add_product_accordion_meta_box() {
    add_meta_box(
        'product_accordion',
        'Accordion Sections',
        'render_product_accordion_meta_box',
        'product',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_product_accordion_meta_box');

// Render meta box
function render_product_accordion_meta_box($post) {
    wp_nonce_field('product_accordion_meta_box', 'product_accordion_meta_box_nonce');
    
    $accordion_data = get_post_meta($post->ID, '_product_accordion', true);
    if (!is_array($accordion_data)) {
        $accordion_data = array();
    }
    ?>
    <div id="accordion_sections">
        <?php foreach ($accordion_data as $index => $section): ?>
        <div class="accordion-section" style="margin-bottom: 15px; padding: 10px; border: 1px solid #ddd;">
            <input type="text" 
                   name="accordion_title[]" 
                   value="<?php echo esc_attr($section['title']); ?>" 
                   placeholder="Title"
                   style="width: 100%; margin-bottom: 10px;">
            <textarea name="accordion_content[]" 
                      placeholder="Content" 
                      style="width: 100%; height: 100px;"><?php echo esc_textarea($section['content']); ?></textarea>
            <button type="button" class="button remove-section" style="margin-top: 5px;">Remove</button>
        </div>
        <?php endforeach; ?>
    </div>
    <button type="button" id="add_accordion_section" class="button">Add Section</button>

    <script>
    jQuery(document).ready(function($) {
        $('#add_accordion_section').click(function() {
            var section = $('<div class="accordion-section" style="margin-bottom: 15px; padding: 10px; border: 1px solid #ddd;">' +
                '<input type="text" name="accordion_title[]" placeholder="Title" style="width: 100%; margin-bottom: 10px;">' +
                '<textarea name="accordion_content[]" placeholder="Content" style="width: 100%; height: 100px;"></textarea>' +
                '<button type="button" class="button remove-section" style="margin-top: 5px;">Remove</button>' +
                '</div>');
            $('#accordion_sections').append(section);
        });

        $(document).on('click', '.remove-section', function() {
            $(this).parent('.accordion-section').remove();
        });
    });
    </script>
    <?php
}

// Save meta box data
function save_product_accordion_meta_box($post_id) {
    if (!isset($_POST['product_accordion_meta_box_nonce'])) return;
    if (!wp_verify_nonce($_POST['product_accordion_meta_box_nonce'], 'product_accordion_meta_box')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $accordion_data = array();
    if (isset($_POST['accordion_title']) && isset($_POST['accordion_content'])) {
        $titles = $_POST['accordion_title'];
        $contents = $_POST['accordion_content'];
        
        for ($i = 0; $i < count($titles); $i++) {
            if (!empty($titles[$i]) || !empty($contents[$i])) {
                $accordion_data[] = array(
                    'title' => sanitize_text_field($titles[$i]),
                    'content' => wp_kses_post($contents[$i])
                );
            }
        }
    }
    
    update_post_meta($post_id, '_product_accordion', $accordion_data);
}
add_action('save_post_product', 'save_product_accordion_meta_box');

// Shortcode function
function product_accordion_shortcode($atts) {
    global $post;
    
    $accordion_data = get_post_meta($post->ID, '_product_accordion', true);
    if (empty($accordion_data)) return '';

    $output = '<div id="accordion-collapse" data-accordion="collapse">';
    
    foreach ($accordion_data as $index => $section) {
        $heading_id = 'accordion-collapse-heading-' . ($index + 1);
        $body_id = 'accordion-collapse-body-' . ($index + 1);
        
        $output .= '
        <h2 id="'.$heading_id.'">
            <button type="button" 
                    class="flex items-center justify-between w-full p-5 font-medium text-left text-gray-500 hover:text-gray-500 focus:text-gray-500 hover:bg-white focus:bg-white border border-gray-200 gap-3" 
                    data-accordion-target="#'.$body_id.'" 
                    aria-expanded="false" 
                    aria-controls="'.$body_id.'">
                <span>'.esc_html($section['title']).'</span>
                <svg class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5"/>
                </svg>
            </button>
        </h2>
        <div id="'.$body_id.'" class="hidden" aria-labelledby="'.$heading_id.'">
            <div class="p-5 border border-gray-200">
                <p class="text-gray-500">'.wp_kses_post($section['content']).'</p>
            </div>
        </div>';
    }
    
    $output .= '</div>';

    // Add Tailwind CSS
    wp_enqueue_style('tailwindcss', 'https://cdn.tailwindcss.com');

    // Add JavaScript functionality
    $output .= "
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const accordionButtons = document.querySelectorAll('[data-accordion-target]');
        
        accordionButtons.forEach(button => {
            button.addEventListener('click', function() {
                const target = this.getAttribute('data-accordion-target');
                const content = document.querySelector(target);
                const isExpanded = this.getAttribute('aria-expanded') === 'true';
                
                // Toggle aria-expanded
                this.setAttribute('aria-expanded', !isExpanded);
                
                // Toggle content visibility
                if (content.classList.contains('hidden')) {
                    content.classList.remove('hidden');
                } else {
                    content.classList.add('hidden');
                }
                
                // Rotate arrow
                const arrow = this.querySelector('svg');
                if (isExpanded) {
                    arrow.classList.add('rotate-180');
                } else {
                    arrow.classList.remove('rotate-180');
                }
            });
        });
    });
    </script>";

    return $output;
}
add_shortcode('product_accordion', 'product_accordion_shortcode');
