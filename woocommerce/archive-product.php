<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

/**
 * Hook: woocommerce_before_main_content.
 */
do_action( 'woocommerce_before_main_content' );

?>
<header class="woocommerce-products-header">
    <?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
        <div class="max-w-[1440px] w-full mx-auto px-5 py-[15px] md:px-[20px] md:py-[30px]">
            <div class="text-center">
                <div class="w-full">
                    <h1 class="text-[#2F2C27] font-['PP_Neue_Machina'] text-[40px] md:text-7xl font-normal leading-[1.2] md:leading-[74.6px] uppercase woocommerce-products-header__title page-title">
                        <?php woocommerce_page_title(); ?>
                    </h1>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php
    /**
     * Hook: woocommerce_archive_description.
     */
    do_action( 'woocommerce_archive_description' );
    ?>
</header>

<?php
if ( woocommerce_product_loop() ) {
    /**
     * Hook: woocommerce_before_shop_loop.
     */
    do_action( 'woocommerce_before_shop_loop' );
    ?>
	<style>
		@media (max-width: 767px) {
			.view-button-text {
				font-size: 16px!important;
			}
		}
</style>

    <div class="max-w-[1440px] w-full mx-auto py-[15px] md:px-[20px] md:py-[30px]">
        <div class="w-full grid grid-cols-2 md:grid-cols-3 gap-6">
            <?php
            while ( have_posts() ) {
                the_post();
                global $product;
                ?>
                <div class="flex flex-col gap-6">
                    <a href="<?php echo get_permalink(); ?>">
                        <?php
                        $image_id = $product->get_image_id();
                        $image_url = wp_get_attachment_image_url($image_id, 'full');
                        ?>
                        <img class="w-full" src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($product->get_name()); ?>">
                    </a>
                    <h3 class="text-[#2F2C27] font-['PP_Neue_Machina'] text-[36px] font-normal uppercase">
                        <a href="<?php echo get_permalink(); ?>" style="color: #2F2C27" class="text-[#2F2C27] hover:text-[#2F2C27]"><?php echo $product->get_name(); ?></a>
                    </h3>
                    <p class="text-[#2F2C27] font-['PP_Neue_Machina'] text-[24px] font-normal uppercase">
                        <?php echo $product->get_short_description(); ?>
                    </p>
                    <span class="text-[#2F2C27] font-['PP_Neue_Machina'] text-3xl font-normal leading-[111.637px] uppercase">
                        <?php echo $product->get_price(); ?>$
                    </span>
                    <div class="flex items-center gap-4 justify-end">
                        <a href="<?php echo get_permalink(); ?>" class="relative w-fit cursor-pointer view-button">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/icons/button-circles.svg" 
                                 alt="button background" 
                                 class="w-56">
                            <span class="view-button-text absolute inset-0 flex items-center justify-center text-xl text-black font-['PP_Neue_Machina'] uppercase">
                                VIEW
                            </span>
                        </a>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>

    <?php
    /**
     * Hook: woocommerce_after_shop_loop.
     */
    do_action( 'woocommerce_after_shop_loop' );
} else {
    /**
     * Hook: woocommerce_no_products_found.
     */
    do_action( 'woocommerce_no_products_found' );
}

/**
 * Hook: woocommerce_after_main_content.
 */
do_action( 'woocommerce_after_main_content' );

/**
 * Hook: woocommerce_sidebar.
 */
do_action( 'woocommerce_sidebar' );

get_footer( 'shop' );
