<?php
class Branch_Story_Grid extends \Elementor\Widget_Base {
    
    public function get_name() {
        return 'branch_story_grid';
    }
    
    public function get_title() {
        return 'Branch Story Grid';
    }
    
    public function get_icon() {
        return 'eicon-gallery-grid';
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

        // სურათები
        for($i = 1; $i <= 4; $i++) {
            $this->add_control(
                'image_' . $i,
                [
                    'label' => 'Image ' . $i,
                    'type' => \Elementor\Controls_Manager::MEDIA,
                    'default' => [
                        'url' => \Elementor\Utils::get_placeholder_image_src(),
                    ],
                ]
            );
        }

        // ტექსტი
        $this->add_control(
            'title',
            [
                'label' => 'Title',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'YOUR STORY GOES HERE'
            ]
        );

        // ღილაკი
        $this->add_control(
            'button_text',
            [
                'label' => 'Button Text',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'READ MORE'
            ]
        );

        $this->add_control(
            'button_link',
            [
                'label' => 'Button Link',
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => 'https://your-link.com',
                'default' => [
                    'url' => '#',
                    'is_external' => false,
                    'nofollow' => false,
                ],
            ]
        );

        $this->end_controls_section();
    }
    
    protected function render() {
        $settings = $this->get_settings_for_display();
        ?>
        <style>
            .story-grid-image {
                width: 100%!important;
                height: 100%!important;
                object-fit: cover;
            }

            .story-grid-content h2 {
                white-space: pre-line;
                line-height: 140px!important;
                font-size: 140px;
                font-family: 'Arial', sans-serif;
            }

            .story-grid-container {
                max-width: 1440px !important;
                margin: 0 auto !important;
            }

            @media (max-width: 1024px) {
                .story-grid-wrapper {
                    padding: 15px 0px!important;
                }
                
                .story-grid-container {
                    width: 100% !important;
                    min-width: auto !important;
					padding: 0px 20px;
                    flex-direction: column !important;
                }

                .story-grid-images {
                    width: 100% !important;
                }

                .story-grid-content {
                    width: 100% !important;
                    align-items: flex-start !important;
                    text-align: center !important;
                }

                .story-grid-content h2 {
                    font-size: 40px !important;
                    line-height: 70px !important;
                    text-align: start !important;
                    margin-top: 20px !important;
                    white-space: pre-line !important;
                    width: 100% !important;
                }

                .story-grid-content .button-wrapper {
                    margin-top: 20px !important;
                    margin-bottom: 20px !important;
                    align-self: flex-start !important;
                }
            }

            @media (max-width: 767px) {
                .story-grid-content h2 {
                    font-size: 30px !important;
                    line-height: 50px !important;
                }

                .story-grid-content .button-wrapper img {
                    width: 180px !important;
                }

                .story-grid-content .button-wrapper span {
                    font-size: 16px !important;
                }
            }
        </style>
        <div class="story-grid-wrapper w-full flex flex-col gap-12 py-12">
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/icons/story_top_line.svg" alt="line">

            <div class="story-grid-container flex gap-6 items-center justify-between">
                <div class="story-grid-images w-2.5/6 gap-4 flex justify-start">
                    <div class="w-1/2 flex flex-col items-center justify-between gap-4 mt-4">
                        <img class="story-grid-image w-full" src="<?php echo $settings['image_1']['url']; ?>" alt="image 1">
                        <img class="story-grid-image w-full" src="<?php echo $settings['image_2']['url']; ?>" alt="image 2">
                    </div>

                    <div class="w-1/2 flex flex-col items-center justify-between gap-4 mb-4">
                        <img class="story-grid-image w-full" src="<?php echo $settings['image_3']['url']; ?>" alt="image 3">
                        <img class="story-grid-image w-full" src="<?php echo $settings['image_4']['url']; ?>" alt="image 4">
                    </div>
                </div>

                <div class="story-grid-content w-2/6 flex flex-col gap-8 items-end">
                    <h2 class="font-normal leading-tight text-right">
                        <?php echo str_replace(["\n", "\r", "<br>", "<br/>", "<br />"], " ", $settings['title']); ?>
                    </h2>
                    
                    <div class="button-wrapper relative">
                        <a href="<?php echo $settings['button_link']['url']; ?>" 
                           class="story-main-heading text-black hover:text-gray-700 transition-colors relative z-10"
                           <?php echo $settings['button_link']['is_external'] ? 'target="_blank"' : ''; ?>
                           <?php echo $settings['button_link']['nofollow'] ? 'rel="nofollow"' : ''; ?>>
                            <div class="relative">
                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/icons/button-circles.svg" 
                                     alt="button background" 
                                     class="w-56">
                                <span class="absolute inset-0 flex items-center justify-center text-xl">
                                    <?php echo $settings['button_text']; ?>
                                </span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/icons/story_bottom_line.svg" alt="line">
        </div>
        <?php
    }
}