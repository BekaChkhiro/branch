<?php
/**
 * Thankyou page
 *
 * @package WooCommerce\Templates
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="w-full py-4 md:py-8">
    <?php if ( $order ) :
        do_action( 'woocommerce_before_thankyou', $order->get_id() ); ?>

        <?php if ( $order->has_status( 'failed' ) ) : ?>
            <p class="bg-red-100 text-red-700 p-4 rounded mb-4"><?php esc_html_e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ); ?></p>

            <p class="flex gap-4">
                <a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="inline-block px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"><?php esc_html_e( 'Pay', 'woocommerce' ); ?></a>
                <?php if ( is_user_logged_in() ) : ?>
                    <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="inline-block px-6 py-2 bg-gray-600 text-white rounded hover:bg-gray-700"><?php esc_html_e( 'My account', 'woocommerce' ); ?></a>
                <?php endif; ?>
            </p>

        <?php else : ?>
            <div class="w-full text-white py-6 px-4 md:px-6 text-center relative mb-8 md:mb-12 rounded-lg flex flex-col md:flex-row justify-between items-center" style="background-color: #5F849C;">
                <div class="hidden md:flex md:w-1/5 flex-col items-start">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/icons/thank-you-page-left-icon.svg" class="mb-32" alt="tree">
				</div>

                <div class="w-full md:max-w-3/5 mx-auto flex flex-col items-center gap-4 md:gap-6">
                    <p class="text-lg md:text-xl mb-0 md:mb-2">Order Confirmation</p>
                    <h1 class="text-white text-center text-4xl md:text-7xl leading-tight md:leading-30 uppercase">THANK YOU FOR <br>YOUR ORDER!</h1>
                    <p class="text-base md:text-xl px-4">we've received your order and will contact you as soon as your package is shipped. <br class="hidden md:block">You can find your purchase information below.</p>
                </div>

                <div class="hidden md:flex md:w-1/5 flex-col items-end">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/icons/thank-you-page-right-icon.svg" class="mt-32" alt="tree">
				</div>
            </div>

            <div class="rounded-lg shadow-sm">
                <h2 class="text-2xl font-bold text-center uppercase mb-12">ORDER SUMMARY</h2>
                
                <?php
                $items = $order->get_items();
                foreach ( $items as $item ) {
                    $product = $item->get_product();
                    $image = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'single-post-thumbnail' );
                    ?>
                    <div class="flex gap-4 items-start border-b border-[#EFEFEF] py-4 last:border-b-0">
                        <div class="w-[120px]">
                            <?php echo $image ? '<img src="' . esc_url( $image[0] ) . '" style="width: 120px;" alt="' . esc_attr( $item->get_name() ) . '" class="w-full h-auto object-cover">' : ''; ?>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-base font-normal text-[#1D1D1B] mb-4"><?php echo strtolower(esc_html( $item->get_name() )); ?> (<?php echo esc_html( $item->get_meta('pa_size') ); ?>)</h3>
                            <p class="text-sm uppercase mb-1 text-[#1D1D1B]">SIZE: <?php echo esc_html( $item->get_meta('pa_size') ); ?></p>
                            <p class="text-sm uppercase mb-4 text-[#1D1D1B]">QUANTITY: <?php echo esc_html( $item->get_quantity() ); ?></p>
                        </div>
                        <div class="text-right">
                            <p class="text-base font-normal"><?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?></p>
                        </div>
                    </div>
                <?php } ?>

                <div class="flex flex-col md:flex-row justify-between gap-8 mt-8">
                    <div class="w-full md:w-1/3">
                        <h3 class="text-lg font-semibold mb-2">Payment Details</h3>
                        <p class="text-gray-600"><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></p>
                    </div>
                    
                    <div class="w-full md:w-1/3 space-y-4">
                        <div class="flex justify-between text-base">
                            <span class="text-gray-600">SUBTOTAL</span>
                            <span><?php echo wp_kses_post( $order->get_subtotal_to_display() ); ?></span>
                        </div>
                        <div class="flex justify-between text-base">
                            <span class="text-gray-600">SHIPPING</span>
                            <span><?php echo wp_kses_post( $order->get_shipping_total() ); ?></span>
                        </div>
                        <?php if ( wc_tax_enabled() ) : ?>
                        <div class="flex justify-between text-base">
                            <span class="text-gray-600">VAT/TAX</span>
                            <span><?php echo wp_kses_post( $order->get_total_tax() ); ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="flex justify-between pt-4 font-semibold text-lg border-t border-gray-200">
                            <span>TOTAL</span>
                            <span><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></span>
                        </div>
                    </div>
                </div>
            </div>

        <?php endif; ?>

        <?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
        <?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>

    <?php else : ?>
        <p class="bg-green-50 text-green-700 p-4 rounded">
            <?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you. Your order has been received.', 'woocommerce' ), null ); ?>
        </p>
    <?php endif; ?>
</div>
