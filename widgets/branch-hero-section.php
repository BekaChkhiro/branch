<?php
class Branch_Hero_Section extends \Elementor\Widget_Base {
    
    public function get_name() {
        return 'branch_hero_section';
    }
    
    public function get_title() {
        return 'Branch Hero Section';
    }
    
    public function get_icon() {
        return 'eicon-banner';
    }
    
    public function get_categories() {
        return ['branch'];
    }
    
    protected function _register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => 'Content',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'background_image',
            [
                'label' => 'Background Image',
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_control(
            'background_position_x',
            [
                'label' => 'Background Horizontal Position',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'center',
                'options' => [
                    'left' => 'Left',
                    'center' => 'Center',
                    'right' => 'Right',
                ],
            ]
        );

        $this->add_control(
            'background_position_y',
            [
                'label' => 'Background Vertical Position',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'center',
                'options' => [
                    'top' => 'Top',
                    'center' => 'Center',
                    'bottom' => 'Bottom',
                ],
            ]
        );

        $this->add_control(
            'desktop_height',
            [
                'label' => 'Desktop Height (vh)',
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 90,
                'min' => 20,
                'max' => 100,
            ]
        );

        $this->add_control(
            'tablet_height',
            [
                'label' => 'Tablet Height (vh)',
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 80,
                'min' => 20,
                'max' => 100,
            ]
        );

        $this->add_control(
            'mobile_height',
            [
                'label' => 'Mobile Height (vh)',
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 50,
                'min' => 20,
                'max' => 100,
            ]
        );

        $this->end_controls_section();
    }
    
    protected function render() {
        $settings = $this->get_settings_for_display();
        ?>
        <style>
            @media (max-width: 767px) {
                .branch-hero-section {
                    height: <?php echo $settings['mobile_height']; ?>vh !important;
                }
            }
            
            @media (min-width: 768px) and (max-width: 1024px) {
                .branch-hero-section {
                    height: <?php echo $settings['tablet_height']; ?>vh !important;
                }
            }
        </style>
        <div class="branch-hero-section relative w-full bg-cover bg-center" 
             style="background-image: url('<?php echo $settings['background_image']['url']; ?>'); 
                    background-position: <?php echo $settings['background_position_x']; ?> <?php echo $settings['background_position_y']; ?>;
                    height: <?php echo $settings['desktop_height']; ?>vh;">
            <div class="mx-auto max-w-[1440px] h-full flex items-end justify-center">
            </div>
        </div>
        <?php
    }
}