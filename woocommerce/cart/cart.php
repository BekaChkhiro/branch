<?php
/**
 * Cart Page
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' ); ?>

<form class="woocommerce-cart-form " action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post" style="max-width: 1440px; margin: 0px; padding: 0px;">
    <?php wp_nonce_field('woocommerce-cart'); ?>
    <?php do_action( 'woocommerce_before_cart_table' ); ?>

    <div class="shop_table cart woocommerce-cart-form__contents">
        <div class="cart-header">
            <div class="product-header"><?php esc_html_e( 'PRODUCT', 'woocommerce' ); ?></div>
            <div class="total-header"><?php esc_html_e( 'TOTAL', 'woocommerce' ); ?></div>
        </div>

        <?php do_action( 'woocommerce_before_cart_contents' ); ?>

        <?php
        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
            $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
            $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

            if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                ?>
                <div class="cart-item">
                    <div class="product-info">
                        <div class="product-thumbnail">
                            <?php
                            $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
                            echo $thumbnail;
                            ?>
                        </div>

                        <div class="product-details">
                            <h3 class="product-name">
                                <?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) ); ?>
                            </h3>
                            <div class="product-meta">
                                <?php echo wc_get_formatted_cart_item_data( $cart_item ); ?>
                                <div class="size">
                                    <?php echo esc_html__('SIZE', 'woocommerce') . ' ' . $_product->get_attribute('size'); ?>
                                </div>
                            </div>

                            <div class="quantity-controls">
                                <button type="button" class="minus">-</button>
                                <?php
                                if ( $_product->is_sold_individually() ) {
                                    $min_quantity = 1;
                                    $max_quantity = 1;
                                } else {
                                    $min_quantity = 0;
                                    $max_quantity = $_product->get_max_purchase_quantity();
                                }

                                $product_quantity = woocommerce_quantity_input(
                                    array(
                                        'input_name'   => "cart[{$cart_item_key}][qty]",
                                        'input_value'  => $cart_item['quantity'],
                                        'max_value'    => $max_quantity,
                                        'min_value'    => $min_quantity,
                                        'product_name' => $_product->get_name(),
                                    ),
                                    $_product,
                                    false
                                );

                                echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item );
                                ?>
                                <button type="button" class="plus">+</button>
                            </div>
                        </div>
                    </div>

                    <div class="product-total flex flex-col justify-between">
                        <?php
                            echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
                        ?>
                        <div class="remove-item">
                            <?php
                                echo apply_filters(
                                    'woocommerce_cart_item_remove_link',
                                    sprintf(
                                        '<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s"><span class="remove-text">%s</span></a>',
                                        esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                                        esc_html__( 'Remove this item', 'woocommerce' ),
                                        esc_attr( $product_id ),
                                        esc_attr( $_product->get_sku() ),
                                        esc_html__( 'REMOVE FROM CART', 'woocommerce' )
                                    ),
                                    $cart_item_key
                                );
                            ?>
                        </div>
                    </div>
                </div>
                <?php
            }
        }
        ?>

        <?php do_action( 'woocommerce_cart_contents' ); ?>

        <div class="cart-totals">
            <div class="order-total-row">
                <span class="label"><?php esc_html_e( 'ORDER TOTAL', 'woocommerce' ); ?></span>
                <span class="value"><?php wc_cart_totals_order_total_html(); ?></span>
            </div>
        </div>

        <?php do_action( 'woocommerce_after_cart_contents' ); ?>
    </div>

    <?php do_action( 'woocommerce_after_cart_table' ); ?>

    <div class="flex justify-between">
        <button type="submit" class="button update-cart branch-button" name="update_cart" value="true">
            <?php esc_html_e( 'Update cart', 'woocommerce' ); ?>
        </button>
        <button type="submit" class="checkout-button branch-button" name="proceed" value="<?php esc_attr_e( 'GO TO CHECKOUT', 'woocommerce' ); ?>">
            <?php esc_html_e( 'GO TO CHECKOUT', 'woocommerce' ); ?>
        </button>
    </div>

    <?php do_action( 'woocommerce_after_cart' ); ?>
</form>

<style>
.branch-button {
    background: url("<?php echo get_stylesheet_directory_uri(); ?>/assets/icons/button-circles.svg' ?>")!important;
    background-size: 100% 100% !important;
    background-repeat: no-repeat !important;
    background-position: center center !important;
    color: #000 !important;
    border: none!important;
    font-size: 14px!important
}


.woocommerce-cart-form {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

@media screen and (max-width: 768px) {
    .woocommerce-cart-form {
        padding: 15px 20px!important;
    }
}

.cart-header {
    display: flex;
    justify-content: space-between;
    padding-bottom: 20px;
    border-bottom: 1px solid #e5e5e5;
    font-size: 14px;
}

.cart-item {
    display: flex;
    justify-content: space-between;
    padding: 30px 0;
    border-bottom: 1px solid #e5e5e5;
}

.product-info {
    display: flex;
    gap: 20px;
}

.product-thumbnail img {
    width: 150px;
    height: auto;
}

.product-name {
    font-size: 16px;
    margin: 0 0 10px;
}

.product-meta {
    font-size: 14px;
    color: #666;
    margin-bottom: 15px;
}

.quantity-controls {
    display: flex;
    align-items: center;
    gap: 0;
    border: 1px solid #E5E5E5;
    border-radius: 4px;
    width: fit-content;
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
}

.quantity input {
    width: 32px;
    height: 32px;
    text-align: center;
    border: none;
    font-size: 14px;
    padding: 0;
    margin: 0;
    -moz-appearance: textfield;
}

.quantity input::-webkit-outer-spin-button,
.quantity input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.minus {
    border-right: 1px solid #E5E5E5 !important;
}

.plus {
    border-left: 1px solid #E5E5E5 !important;
}

.product-total {
    text-align: right;
    font-size: 22px;
    color: #2F2C27;
}

.remove-item {
    margin-top: 10px;
}

.remove-item a {
    color: #FF0000;
    text-decoration: none;
    font-size: 12px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.remove-item a:hover {
    text-decoration: underline;
}

.cart-totals {
    margin-top: 30px;
    text-align: right;
}

.cart-totals > div {
    margin: 10px 0;
}

.cart-totals .label {
    margin-right: 20px;
}

.checkout-button-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    margin-top: 30px;
}

.checkout-button {
    display: inline-block;
    padding: 15px 40px;
    font-size: 16px;
    cursor: pointer;
    position: relative;
    border-radius: 30px;
}

.checkout-button::after {
    display: none;
}

.update-cart {
    display: inline-block;
    padding: 15px 40px;
    background: white;
    color: #000;
    border-radius: 30px;
    font-size: 16px;
    cursor: pointer;
    position: relative;
}

.update-cart:hover {
    background: #f5f5f5;
}

.update-cart::after {
    display: none;
}

.update-cart {
    display: inline-block;
    padding: 15px 40px;
    background: white;
    color: #000;
    border-radius: 30px;
    font-size: 16px;
    cursor: pointer;
    position: relative;
}

.update-cart:hover {
    background: #f5f5f5;
}

.update-cart::after {
    display: none;
}

.woocommerce a.remove {
    width: 100%;
    color: #E10000!important;
    font-size: 16px;
}

.woocommerce a.remove:hover {
    background: white;
    color: #E10000!important;
}

.woocommerce-cart-form .quantity-controls {
    border-radius: 20px;
    background: white;
}

.woocommerce-cart-form .quantity-controls .minus:hover, .woocommerce-cart-form .quantity-controls .minus:active, .woocommerce-cart-form .quantity-controls .minus:focus, .woocommerce-cart-form .quantity-controls .plus:hover, .woocommerce-cart-form .quantity-controls .plus:active, .woocommerce-cart-form .quantity-controls .plus:focus {
    color: #2F2C27!important;
    border-radius: 20px;
    background: #f5f5f5!important;
}

.woocommerce-cart-form .quantity {
    border: none!important;
    background: #fef9f3!important;
}

#quantity_67601a3f88512 {
    background: none!important;
}

.cart-item.updating {
    opacity: 0.5;
    pointer-events: none;
}

/* Tablet Styles */
@media screen and (max-width: 768px) {
    .woocommerce-cart-form {
        padding: 15px 20px;
    }

    .cart-header {
        padding-bottom: 15px;
        font-size: 12px;
    }

    .cart-item {
        padding: 20px 0;
    }

    .product-thumbnail img {
        width: 100px;
    }

    .product-name {
        font-size: 14px;
    }

    .product-total {
        font-size: 18px;
    }

    .checkout-button-wrapper {
        flex-direction: column;
        gap: 10px;
    }

    .checkout-button, .update-cart {
        width: 100%;
        padding: 12px 30px;
        font-size: 14px;
    }
}

/* Mobile Styles */
@media screen and (max-width: 480px) {
    .woocommerce-cart-form {
        padding: 15px 20px;
    }

    .cart-header {
        padding-bottom: 15px;
        font-size: 12px;
    }

    .cart-item {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        padding: 20px 0;
        gap: 10px;
    }

    .product-info {
        display: flex;
        flex-direction: row;
        gap: 10px;
    }

    .product-thumbnail img {
        width: 80px;
    }

    .product-details {
        width: auto;
        text-align: left;
    }

    .product-name {
        font-size: 14px;
    }

    .product-meta {
        font-size: 12px;
    }

    .quantity-controls {
        margin: 10px 0;
    }

    .product-total {
        text-align: right;
        font-size: 16px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .remove-item {
        margin-top: 5px;
        text-align: right;
    }

    .remove-item a {
        color: #E10000;
        text-decoration: none;
        font-size: 10px;
        font-weight: 500;
        text-transform: uppercase;
    }

    .remove-item .remove-text {
        display: none;
    }

    .remove-item a:after {
        content: 'Remove';
        font-size: 10px;
    }

    .cart-totals {
        text-align: right;
        margin-top: 20px;
    }

    .checkout-button-wrapper {
        margin-top: 20px;
        flex-direction: column;
        gap: 10px;
    }

    .checkout-button, .update-cart {
        width: 100%;
        padding: 12px 20px;
        font-size: 13px;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    function initializeCartControls() {
        // Enable update cart button when quantity changes
        function enableUpdateCart() {
            $('button[name="update_cart"]').prop('disabled', false);
        }

        // Handle quantity changes with plus/minus buttons
        $('.quantity-controls .minus').off('click').on('click', function(e) {
            e.preventDefault();
            var input = $(this).siblings('.quantity').find('input');
            var value = parseInt(input.val());
            if (value > 1) {
                input.val(value - 1);
                enableUpdateCart();
            }
        });

        $('.quantity-controls .plus').off('click').on('click', function(e) {
            e.preventDefault();
            var input = $(this).siblings('.quantity').find('input');
            var value = parseInt(input.val());
            input.val(value + 1);
            enableUpdateCart();
        });

        // Handle direct input changes
        $('.quantity input').off('change').on('change', function() {
            enableUpdateCart();
        });

        // Initially disable update cart button
        $('button[name="update_cart"]').prop('disabled', true);
    }

    // Initialize controls on page load
    initializeCartControls();

    // Re-initialize controls after cart update
    $(document.body).on('updated_cart_totals', function() {
        initializeCartControls();
    });
});
</script>