<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://swasis.com
 * @since      0.0.7
 *
 * @package    FISIF_Tools
 * @subpackage FISIF_Tools/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    FISIF_Tools
 * @subpackage FISIF_Tools/public
 * @author     James S. Moore <james@teamweb.us>
 */
class FISIF_Tools_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ft-member-home.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ft-account-receivable-report.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ft-payroll-report.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ft-premium-computation.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ft-loss-run-report.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ft-loss-ratio-report.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ft-certificate-of-insurance.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ft-agent-search.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ft-agent-home.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ft-agent-commission-report.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ft-agent-premium-estimator.php';
    }

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    0.0.7
	 */
	public function enqueue_styles() {

		// force concatenation of styles to avoid sending output to allow
		// PDF generation to return appropriate headers
		//global $wp_styles;
		//$wp_styles->do_concat = TRUE;

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/fisif-tools-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		 // force concatenation of scripts to avoid sending output to allow
		 // PDF generation to return appropriate headers
		 //global $concatenate_scripts;
		 //global $wp_scripts;
		 //$wp_scripts->do_concat = TRUE;
		 //$concatenate_scripts = TRUE;

		 // kill script output
		 //$wp_scripts->print_html = '';

		 wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/fisif-tools-public.js', array( 'jquery' ), $this->version, false );

	}


	/**
	 * Destroy all $_SESSION data on WP logout
	 *
	 * @since     0.0.7
	 * @return    string    The version number of the plugin.
	 */
	public function destroy_session_on_logout() {
        if (isset($_SESSION['fisifmemberid'])) {
            unset($_SESSION['fisifmemberid']);
        }
				if (isset($_SESSION['fisiftools'])) {
            unset($_SESSION['fisiftools']);
        }
    }

}
?>
