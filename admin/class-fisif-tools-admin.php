<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://swasis.com
 * @since      0.0.7
 *
 * @package    FISIF_Tools
 * @subpackage FISIF_Tools/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    FISIF_Tools
 * @subpackage FISIF_Tools/admin
 * @author     Your Name <email@example.com>
 */
class FISIF_Tools_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.0.7
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.0.7
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.0.7
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->load_dependencies();

	}

	public function load_dependencies() {

		/**
		 * The classes responsible for the members home page and related report.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ft-pdf-export-settings.php';
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    0.0.7
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in FISIF_Tools_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The FISIF_Tools_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/fisif-tools-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    0.0.7
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in FISIF_Tools_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The FISIF_Tools_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/fisif-tools-admin.js', array( 'jquery' ), $this->version, false );

	}
	public function ft_admin_init(){

		//register_setting( 'plugin_options', 'plugin_options', 'plugin_options_validate' );
		/*
		add_action('admin_init', 'plugin_admin_init');
		register_setting( 'plugin_options', 'plugin_options', 'plugin_options_validate' );
		add_settings_section('plugin_main', 'Main Settings', 'plugin_section_text', 'plugin');
		add_settings_field('plugin_text_string', 'Plugin Text Input', 'plugin_setting_string', 'plugin', 'plugin_main');
		*/
		register_setting('fisiftools-main', 'ft-loginid');
		add_settings_section('fisiftools-main', 'Main Settings', 'ft_main_options_page', 'fisif-tools');
		add_settings_field('ft-loginid', 'Login ID:', 'plugin_setting_string', 'fisif-tools', 'fisiftools-main');

		/*
		add_settings_field(
			'myprefix_setting-id',
			'This is the setting title',
			'myprefix_setting_callback_function',
			'general',
			'myprefix_settings-section-name',
			array( 'label_for' => 'myprefix_setting-id' )
		);
		*/
	}
	public function ft_admin_add_page() {

		add_options_page(
			'FISIF Tools Configuration',
			'FISIF Tools',
			'manage_options',
			'fisif-tools',
			array($this, 'ft_main_options_page')
			);

	}
	public function ft_main_options_page() {

		echo "<div><h1>FISIF Tools</h1>\n";
		echo "<p>Configuration options for FISIF Tools for Wordpress plugin. </p>";
		echo "<form action=\"options-general.php\" method=\"post\">";
		settings_fields('fisiftools-main');
		do_settings_sections('fisif-tools');

		echo '<input name="Submit" type="submit" value="Save Changes" />';
		echo "</form>";
		echo "</div>";

	}
	public function plugin_setting_string() {
		echo 'plugin setting string!';
		//$options = get_option('ft-loginid');
		//echo "<input id='plugin_text_string' name='plugin_options[text_string]' size='40' type='text' value='{$options['text_string']}' />";
	}
	public function ft_show_settings() {
		echo "Here's my settings!";
	}
	public function ft_import_page() {
		echo "<div><h1>FISIF Tools - XML Import</h1>\n";
		echo "<p>Rebuilding database from XML import data.</p>";
	}
	/**
         * Return a message for testing
         *
         * @since    0.0.7
         * @access   public
         */
  public function fisif_admin_notice() {
          echo "<p id='fisif_admin_notice'>This is the FISIF admin notice!</p>";
  }

	/**
	 * Destroy all $_SESSION data on WP logout
	 *
	 * @since     0.0.7
	 * @return    string    The version number of the plugin.
	 */
	public function destroy_session_on_logout() {
        session_destroy();
	}

}
