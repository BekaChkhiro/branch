<?php
class Branch_Product_Showcase extends \Elementor\Widget_Base {
    
    public function get_name() {
        return 'branch_product_showcase';
    }
    
    public function get_title() {
        return 'Branch Product Showcase';
    }
    
    public function get_icon() {
        return 'eicon-products';
    }
    
    public function get_categories() {
        return ['branch'];
    }
    
    protected function _register_controls() {
        // First Product Section
        $this->start_controls_section(
            'product_1_section',
            [
                'label' => 'First Product',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'product_id_1',
            [
                'label' => 'Select First Product',
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options' => $this->get_products_list(),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'product_1_image_1',
            [
                'label' => 'Additional Image 1',
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => '',
                ],
            ]
        );

        $this->add_control(
            'product_1_image_2',
            [
                'label' => 'Additional Image 2',
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => '',
                ],
            ]
        );

        $this->end_controls_section();

        // Second Product Section
        $this->start_controls_section(
            'product_2_section',
            [
                'label' => 'Second Product',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'product_id_2',
            [
                'label' => 'Select Second Product',
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options' => $this->get_products_list(),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'product_2_image_1',
            [
                'label' => 'Additional Image 1',
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => '',
                ],
            ]
        );

        $this->add_control(
            'product_2_image_2',
            [
                'label' => 'Additional Image 2',
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => '',
                ],
            ]
        );

        $this->end_controls_section();

        // General Settings Section
        $this->start_controls_section(
            'general_settings_section',
            [
                'label' => 'General Settings',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_price',
            [
                'label' => 'Show Price',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes'
            ]
        );

        $this->add_control(
            'show_description',
            [
                'label' => 'Show Description',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes'
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label' => 'Button Text',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'ADD TO CART'
            ]
        );

        $this->end_controls_section();

        // Style Section
        $this->start_controls_section(
            'style_section',
            [
                'label' => 'Style',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => 'Title Typography',
                'selector' => '{{WRAPPER}} .product-title',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => 'Title Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#000000',
                'selectors' => [
                    '{{WRAPPER}} .product-title' => 'color: {{VALUE}}'
                ]
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
        
        if (empty($settings['product_id_1']) || empty($settings['product_id_2'])) {
            echo 'გთხოვთ აირჩიოთ ორივე პროდუქტი';
            return;
        }

        $product1 = wc_get_product($settings['product_id_1']);
        $product2 = wc_get_product($settings['product_id_2']);
        
        if (!$product1 || !$product2) {
            return;
        }
        ?>
        <div class="flex flex-col gap-12 py-12 px-5 md:px-0 md:py-15 mx-auto" style="max-width: 1440px;">
          <div>
          <div class="w-full md:w-1/2">
            <h3 class="text-[#2F2C27] font-['PP_Neue_Machina'] text-[40px] md:text-7xl font-normal leading-[1.2] md:leading-[74.6px] uppercase product-showcase-title">
                Discover Your New Favorite Hoodie – Limited Edition
            </h3>
          </div>
          </div>

            <div class="w-full flex justify-between items-center gap-6">
                <div class="w-1/2 flex flex-col gap-6">
                <?php 
                  $image1 = wp_get_attachment_image_src(get_post_thumbnail_id($product1->get_id()), 'full');
                ?>
                <a href="<?php echo $product1->get_permalink(); ?>">
                    <img class="w-full" src="<?php echo $image1[0]; ?>" alt="<?php echo $product1->get_name(); ?>">
                </a>
                <h3 class="text-[#2F2C27] font-['PP_Neue_Machina'] text-[36px] font-normal uppercase">
                    <a href="<?php echo $product1->get_permalink(); ?>"><?php echo $product1->get_name(); ?> (01)</a>
                </h3>
                <p class="text-[#2F2C27] font-['PP_Neue_Machina'] text-[24px] font-normal uppercase">
                    <?php echo $product1->get_short_description(); ?>
                </p>
                <span class="text-[#2F2C27] font-['PP_Neue_Machina'] text-3xl font-normal leading-[111.637px] uppercase">
                    <?php echo $product1->get_price(); ?>$
                </span>
                <div class="flex items-center gap-4 justify-end">
                    <a href="<?php echo $product1->get_permalink(); ?>" class="relative w-fit cursor-pointer view-button">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/icons/button-circles.svg" 
                             alt="button background" 
                             class="w-56">
                        <span class="absolute inset-0 flex items-center justify-center text-xl text-black uppercase">
                            VIEW
                        </span>
                    </a>
                </div>
                </div>

                <div class="w-1/2 flex flex-col gap-6">
                <?php 
                  $image2 = wp_get_attachment_image_src(get_post_thumbnail_id($product2->get_id()), 'full');
                ?>
                <a href="<?php echo $product2->get_permalink(); ?>">
                    <img class="w-full" src="<?php echo $image2[0]; ?>" alt="<?php echo $product2->get_name(); ?>">
                </a>
                <h3 class="text-[#2F2C27] font-['PP_Neue_Machina'] text-[36px] font-normal uppercase">
                    <a href="<?php echo $product2->get_permalink(); ?>"><?php echo $product2->get_name(); ?> (02)</a>
                </h3>
                <p class="text-[#2F2C27] font-['PP_Neue_Machina'] text-[24px] font-normal uppercase">
                    <?php echo $product2->get_short_description(); ?>
                </p>
                <span class="text-[#2F2C27] font-['PP_Neue_Machina'] text-3xl font-normal leading-[111.637px] uppercase">
                    <?php echo $product2->get_price(); ?>$
                </span>
                <div class="flex items-center gap-4 justify-end">
                    <a href="<?php echo $product2->get_permalink(); ?>" class="relative w-fit cursor-pointer view-button">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/icons/button-circles.svg" 
                             alt="button background" 
                             class="w-56">
                        <span class="absolute inset-0 flex items-center justify-center text-xl text-black font-['PP_Neue_Machina'] uppercase">
                            VIEW
                        </span>
                    </a>
                </div>
                </div>
            </div>
          </div>

        <style>
			@media (max-width: 767px) {
				.product-showcase-title {
					font-size: 30px!important;
				}

				.view-button span {
					font-size: 16px!important;
				}
			}
			

            .payment-method-container {
                transition: all 0.3s ease;
            }
            
            .payment-method-container:hover,
            input[type="radio"]:checked + .payment-method-container {
                background-color: #2F2C27;
                color: white;
                border-color: #2F2C27;
            }

            .view-button {
                transition: all 0.3s ease;
            }

            .view-button:hover img {
                filter: brightness(1.2);
            }

            .view-button:hover span {
                color: black;
            }

            h3 a {
                color: inherit;
                text-decoration: none;
                transition: opacity 0.3s ease;
            }

            h3 a:hover {
                opacity: 0.8;
            }

            input[type="text"],
            input[type="tel"],
            input[type="email"] {
                border-color: #2F2C27;
            }

            input[type="text"]:focus,
            input[type="tel"]:focus,
            input[type="email"]:focus {
                border-color: #2F2C27;
                box-shadow: 0 0 0 1px #2F2C27;
            }
        </style>

        <?php
    }
}