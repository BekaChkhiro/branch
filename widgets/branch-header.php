<?php
class Branch_Header extends \Elementor\Widget_Base {
    
    public function get_name() {
        return 'branch_header';
    }
    
    public function get_title() {
        return '';
    }
    
    public function get_icon() {
        return 'eicon-header';
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
            'logo',
            [
                'label' => 'Logo',
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_control(
            'logo_width',
            [
                'label' => 'Logo Width',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 128,
                ],
                'selectors' => [
                    '{{WRAPPER}} .logo-image' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'menu_font_size',
            [
                'label' => 'Menu Font Size',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem'],
                'range' => [
                    'px' => [
                        'min' => 12,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 16,
                ],
                'selectors' => [
                    '{{WRAPPER}} .menu-link' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // Add icon size control
        $this->add_control(
            'icon_size',
            [
                'label' => 'Icon Size',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 12,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .menu-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // Add icon spacing control
        $this->add_control(
            'icon_spacing',
            [
                'label' => 'Icon Spacing',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 8,
                ],
                'selectors' => [
                    '{{WRAPPER}} .menu-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'menu_text',
            [
                'label' => 'Menu Text',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Menu Item',
            ]
        );

        $repeater->add_control(
            'menu_icon',
            [
                'label' => 'Menu Icon',
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => '',
                ],
            ]
        );

        $repeater->add_control(
            'menu_link',
            [
                'label' => 'Link',
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => 'https://your-link.com',
            ]
        );

        $this->add_control(
            'menu_items',
            [
                'label' => 'Menu Items',
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'menu_text' => 'Home',
                        'menu_link' => ['url' => '#'],
                    ],
                ],
                'title_field' => '{{{ menu_text }}}',
            ]
        );

        $this->end_controls_section();
    }
    
    protected function render() {
        $settings = $this->get_settings_for_display();
        ?>
        <header class="fixed top-0 left-0 w-full bg-white/80 backdrop-blur-sm z-50">
            <div class="w-full max-w-full container px-10 py-6 flex items-center justify-between">
                <a href="/" class="logo-wrapper">
                    <img src="<?php echo $settings['logo']['url']; ?>" alt="ლოგო" class="logo-image h-auto">
                </a>
                <nav>
                    <ul class="flex gap-8">
                        <?php foreach ($settings['menu_items'] as $item): ?>
                            <li>
                                <a href="<?php echo $item['menu_link']['url']; ?>" 
                                   class="menu-link text-white hover:text-gray-200 transition-colors flex items-center">
                                    <?php if (!empty($item['menu_icon']['url'])): ?>
                                        <img src="<?php echo $item['menu_icon']['url']; ?>" 
                                             alt="" 
                                             class="menu-icon">
                                    <?php endif; ?>
                                    <?php echo $item['menu_text']; ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
            </div>
        </header>
        <?php
    }
}