<?php

if(!class_exists('FT_PDF_EXPORT_SETTINGS')) {

    class FT_PDF_EXPORT_SETTINGS  {

        public function __construct() {
            add_action('admin_init', array(&$this, 'admin_init'));
            add_action('admin_menu', array(&$this, 'add_menu'));
        }

        // hook into WP's admin_init action hook
        public function admin_init() {
            add_settings_section(
                'ft_pdf_export_settings-section',
                'FTPDF Export Settings',
                array(&$this, 'settings_section_ft_pdf_export_settings'),
                'ft_pdf_export_settings'
            );
        }

        public function settings_section_ft_pdf_export_template()  {
        }

        // ADD MENU
        public function add_menu() {
            add_submenu_page(
                'tools.php',
                'FTPDF Export',
                'FTPDF Export',
                'manage_options',
                'ft_pdf_export_settings',
                array(&$this, 'plugin_settings_page')
            );
        }

        // MENU CALLBACK
        public function plugin_settings_page() {
            if(!current_user_can('manage_options')) {
                wp_die(__('You do not have sufficient permissions to access this page.'));
            }
            ?>
            <div class="wrap">

                <h2>FISIF Tools PDF Exporter</h2>

                <!-- <h3>Custom Export</h3>
                <form method="post" action="?export=FTpdf">
                    <input type="text" name="num" placeholder="Max Posts eg. 3"><br>
                    <input type="text" name="post_type" placeholder="Post Type Slug eg. product"><br>
                    <label for="force">Force new PDF generation?</label><input type="checkbox" name="force" value="force" checked><br>
                    <input type="submit" class="ccsve_button button button-success" value="Custom Export to PDF" name="submit">
                </form> -->

                <h3>Default Export</h3>

                <p>This will export ALL <strong>posts</strong> and will force the generation of a new file.</p>
                <p>If you need to export a custom post type, or you want to customize the layout, number of posts exported or build a frontend custom button, please refer to the <a href="https://wordpress.org/plugins/simple-pdf-exporter/faq/" target="_blank">FAQ</a>.</p>
                <p><strong>Remember that if you don't have at least 512MB of RAM, on this server, the export will fail.</strong></p>
                <a class="ccsve_button button button-success" href="?export=FTpdf&force">Export to PDF</a>
                <br/>
                <p>Here are some url examples you can use (adjust to your needs before pasting it in your browser):</p>
                <ul>
                    <li><a href="<?php echo get_bloginfo('url'); ?>?export=ftpdf&num=1&post_type=portfolio&force"><?php echo get_bloginfo('url'); ?>?export=ftpdf&num=1&post_type=portfolio&force</a></li>
                    <li><a href="<?php echo get_bloginfo('url'); ?>?export=ftpdf&post_id=1234&force"><?php echo get_bloginfo('url'); ?>?export=ftpdf&post_id=1234&force</a></li>
                    <li></li>
                </ul>

          </div>
        <?php
        } // END public function plugin_settings_page()

    } // END class ft_pdf_export_settings

} // END if(!class_exists('ft_pdf_export_settings'))
