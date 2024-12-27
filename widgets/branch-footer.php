<?php
class Branch_Footer extends \Elementor\Widget_Base {
    
    public function get_name() {
        return 'branch_footer';
    }
    
    public function get_title() {
        return 'Branch Footer';
    }
    
    public function get_icon() {
        return 'eicon-footer';
    }
    
    public function get_categories() {
        return ['branch'];
    }
    
    protected function _register_controls() {
        // კონტენტის სექცია
        $this->start_controls_section(
            'content_section',
            [
                'label' => 'კონტენტი',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'customer_care_title',
            [
                'label' => 'Customer Care სათაური',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Customer Care',
            ]
        );

        $this->add_control(
            'company_title',
            [
                'label' => 'Company სათაური',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Company',
            ]
        );

        $this->add_control(
            'legal_area_title',
            [
                'label' => 'Legal Area სათაური',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Legal Area',
            ]
        );

        $this->end_controls_section();
    }
    
    protected function render() {
        $settings = $this->get_settings_for_display();
        ?>
        <footer class="branch-footer w-full text-white p-2 mt-12" style="background-color: #60849c;">
            <div class="mx-auto" style="max-width: 1440px;">
                <div class="w-full flex flex-col lg:flex-row justify-between p-[15px_20px] lg:p-2">
                    <div class="w-full lg:w-10/12 flex-col gap-2">
                      <div class="flex justify-start items-center gap-4 mb-6 lg:mb-0">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/footer_first_image.png" alt="tree">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/the_branch.png" alt="The Branch" style="height: 36px;">
                      </div>

                      <div class="flex flex-col lg:flex-row justify-between gap-6">
                        <div class="w-full lg:w-1/2 flex flex-col lg:flex-row justify-between gap-4">
                          <div class="w-full lg:w-1/3 flex flex-col gap-3 lg:pl-9 lg:pb-9">
                            <h4><?php echo $settings['customer_care_title']; ?></h4>
                            <nav class="flex flex-col gap-2">
                              <a href="#" class="text-white hover:text-gray-300">Delivery</a>
                              <a href="#" class="text-white hover:text-gray-300">Payment</a>
                              <a href="#" class="text-white hover:text-gray-300">Return Policy</a>
                              <a href="#" class="text-white hover:text-gray-300">Sizes</a>
                              <a href="#" class="text-white hover:text-gray-300">Offers</a>
                            </nav>
                          </div>

                          <div class="w-full lg:w-1/3 flex flex-col gap-3 lg:pl-9 lg:pb-9">
                            <h4><?php echo $settings['company_title']; ?></h4>
                            <nav class="flex flex-col gap-2">
                              <a href="#" class="text-white hover:text-gray-300">Delivery</a>
                              <a href="#" class="text-white hover:text-gray-300">Payment</a>
                              <a href="#" class="text-white hover:text-gray-300">Return Policy</a>
                              <a href="#" class="text-white hover:text-gray-300">Sizes</a>
                              <a href="#" class="text-white hover:text-gray-300">Offers</a>
                            </nav>
                          </div>

                          <div class="w-full lg:w-1/3 flex flex-col gap-3 lg:pl-9 lg:pb-9">
                            <h4><?php echo $settings['legal_area_title']; ?></h4>
                            <nav class="flex flex-col gap-2">
                              <a href="#" class="text-white hover:text-gray-300">Delivery</a>
                              <a href="#" class="text-white hover:text-gray-300">Payment</a>
                              <a href="#" class="text-white hover:text-gray-300">Return Policy</a>
                              <a href="#" class="text-white hover:text-gray-300">Sizes</a>
                              <a href="#" class="text-white hover:text-gray-300">Offers</a>
                            </nav>
                          </div>
                        </div>

                        <div class="w-full lg:w-1/2 flex flex-col gap-4">
                            <p class="text-white text-lg">
                                Stay Tuned For The Latest Arrivals And Receive Exclusive, Personalized Offers By Subscribing To The Branch.
                            </p>
                            <div class="relative w-full rounded-xl overflow-hidden border border-white">
                              <input 
                                  type="email" 
                                  placeholder="Your Email" 
                                  style="border: none;"
                                  class="w-full px-5 py-[15px] lg:px-4 lg:py-6 rounded-lg border border-gray-200 text-gray-700 focus:outline-none text-sm bg-white"
                              >
                              <button 
                                  style="border: none; background-color: #5f849d; border-radius: 0px 0px 0px 20px;"
                                  class="absolute right-0 top-0 h-full px-5 lg:px-6 rounded-r-lg bg-white text-white text-sm border-l"
                              >
                                  Subscribe
                              </button>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="w-full lg:w-2/12 flex justify-between items-center mt-6 lg:mt-0">
                      <div class="w-full flex justify-center lg:justify-end">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/footer_second_image.png" alt="tree image">
                      </div>
                    </div>
                </div>
            </div>
        </footer>
        <?php
    }
} 