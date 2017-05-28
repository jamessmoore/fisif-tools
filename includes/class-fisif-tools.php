<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://swasis.com
 * @since      0.0.7
 *
 * @package    FISIF_Tools
 * @subpackage FISIF_Tools/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      0.0.7
 * @package    FISIF_Tools
 * @subpackage FISIF_Tools/includes
 * @author     James S. Moore <james@teamweb.us>
 */

 /**
  * The PDF Export constants and required libs.
  * modeled from 'simple-pdf-exporter' for WordPress
  * @since    0.0.7
  */

	global $pdf_posts_per_page,
	 $pdf_export_post_type,
	 $pdf_export_post_id,
	 $pdf_export_css_file,
	 $pdf_export_final_pdf,
	 $pdf_export_force;

	 // $this->mySqlPrefix = '';
	 $upload_dir = wp_upload_dir();

   // start sessions
   session_start();

	 if (!defined('FT_INCLUDES_DIR'))
			define('FT_INCLUDES_DIR', plugin_dir_path(__FILE__));

//	 if (!defined('FT_PDF_PROCESS'))
//		 define('FT_PDF_PROCESS', FT_PDF_PLUGIN.'process/');
	 if (!defined('FT_PDF_EXPORTDIR'))
		 	define('FT_PDF_EXPORTDIR', $upload_dir['basedir'].'/ft-pdf-export/');

   if (!defined('FT_PDF_IMPORTDIR'))
   		define('FT_PDF_IMPORTDIR', FT_INCLUDES_DIR.'pdf_import/');

	 // DEBUG
	 if (!defined('FT_PDF_HTML_OUTPUT'))
			 define('FT_PDF_HTML_OUTPUT', true);

	 // LAYOUT AND CSS
   if (!defined('FT_PDF_PAGINATION'))
			 define('FT_PDF_PAGINATION', '1');
	 if (!defined('FT_PDF_EXPORTER_CSS_FILE'))
			 define('FT_PDF_EXPORTER_CSS_FILE', get_stylesheet_directory().'/pdf_export.css');
	 if (!defined('FT_PDF_EXPORTER_LAYOUT_FILE'))
			 define('FT_PDF_EXPORTER_LAYOUT_FILE', get_stylesheet_directory().'/pdf_export.php');
	 if (!defined('FT_PDF_EXPORTER_EXTRA_FILE_NAME'))
			 define('FT_PDF_EXPORTER_EXTRA_FILE_NAME', '-');

	 // DOMPDF
	 if (!defined('DOMPDF_PAPER_SIZE'))
			 define('DOMPDF_PAPER_SIZE', 'A4');
	 if (!defined('DOMPDF_DPI'))
			 define('DOMPDF_DPI', 72);
	 if (!defined('DOMPDF_ENABLE_REMOTE'))
			 define('DOMPDF_ENABLE_REMOTE', true);
	 if (!defined('DOMPDF_ENABLE_HTML5'))
			 define('DOMPDF_ENABLE_HTML5', false);
	 if (!defined('DOMPDF_ENABLE_FONTSUBSETTING'))
			 define('DOMPDF_ENABLE_FONTSUBSETTING', true);
	 if (!defined('DOMPDF_MEDIATYPE'))
			 define('DOMPDF_MEDIATYPE', 'print');
	 if (!defined('DOMPDF_FONTHEIGHTRATIO'))
			 define('DOMPDF_FONTHEIGHTRATIO', 1);

   // allow each report to define it's orientation - default to portrait
   // echo "PDF Orientation: ".$_SESSION['fisiftools']['report_orientation'];
   if ($_SESSION['fisiftools']['report_orientation'] == 'landscape'){
     define('DOMPDF_PAPER_ORIENTATION', 'landscape');
   } else {
     define('DOMPDF_PAPER_ORIENTATION', 'portrait');
   }

	 if (FT_PDF_HTML_OUTPUT) {
			 if (!is_dir(FT_PDF_EXPORTDIR.'html/') || !file_exists(FT_PDF_EXPORTDIR.'html/')) {
					 mkdir(FT_PDF_EXPORTDIR.'html/', 0777, true);
			 }
		 }

	require_once(FT_INCLUDES_DIR.'lib/dompdf/autoload.inc.php');
	require_once(FT_INCLUDES_DIR.'lib/fpdf/fpdf.php');
	use Dompdf\Dompdf;
	require_once(FT_INCLUDES_DIR.'lib/fpdi/fpdi.php');
	require_once(FT_INCLUDES_DIR.'lib/fpdi_addon/annots.php');

	if(FT_PDF_PAGINATION && $pdf_export_post_id == '' && $pdf_posts_per_page > 1) {
		require_once(FT_INCLUDES_DIR.'lib/pageno/pdfnumber.php');
		require_once(FT_INCLUDES_DIR.'lib/pageno/pageno.php');
	}
	require_once(FT_INCLUDES_DIR.'lib/pdfmerger/pdfmerger.php');

	// CSS
	if(file_exists(FT_PDF_EXPORTER_CSS_FILE)) {
		$pdf_export_css_file = FT_PDF_EXPORTER_CSS_FILE;
	} else {
		$ft_pdf_css_file = esc_url(plugins_url('public/css/pdf_export.css', dirname(__FILE__)));
	}

	// LAYOUT
	if(file_exists(FT_PDF_EXPORTER_LAYOUT_FILE)) {
		require_once(FT_PDF_EXPORTER_LAYOUT_FILE);
	} else {
		require_once(FT_INCLUDES_DIR.'../public/partials/fisif-tools-public-pdf-layout.php');
	}

	// REPORT-DATA
	if (!defined('FT_REPORT_HTML'))
			define('FT_REPORT_HTML', '<p>Unable to display report data.</p>');

class FISIF_Tools {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    0.0.7
	 * @access   protected
	 * @var      FISIF_Tools_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.0.7
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    0.0.7
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    0.0.7
	 */
	public function __construct() {

		$this->plugin_name = 'fisif-tools';
		$this->version = '0.0.7';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->setup_shortcode();
		$this->setup_pdf();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - FISIF_Tools_Loader. Orchestrates the hooks of the plugin.
	 * - FISIF_Tools_i18n. Defines internationalization functionality.
	 * - FISIF_Tools_Admin. Defines all hooks for the admin area.
	 * - FISIF_Tools_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    0.0.7
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fisif-tools-loader.php';

		/**
		 * The class responsible for orchestrating the database interactions.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fisif-tools-db.php';

		/**
		 * The class responsible for orchestrating the PDF generation / interactions.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fisif-tools-pdf.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fisif-tools-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fisif-tools-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-fisif-tools-public.php';

		$this->loader = new FISIF_Tools_Loader();

	}
	/**
	 * PDF Generation
	 *
	 * @since    0.0.7
	 * @access   private
	 */
	private function setup_pdf() {

		$plugin_pdf = new FISIF_Tools_Pdf();
    $this->loader->add_action('wp_loaded', $plugin_pdf, 'pdf_export_process');
		//$this->loader->add_action( 'plugins_loaded', $plugin_pdf, 'load_plugin_textdomain' );

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the FISIF_Tools_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    0.0.7
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new FISIF_Tools_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * PHP Sessions
	 *
	 * By default WordPress doesn't start PHP Sessions.
     * This method checks for running sessions and starts one if one doesn't exist.
	 *
	 * @since    0.0.7
	 * @access   private
	 */
	private function start_sessions() {

      session_start();
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    0.0.7
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new FISIF_Tools_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
    $this->loader->add_action( 'admin_init', $plugin_admin, 'ft_admin_init' );
    $this->loader->add_action( 'admin_menu', $plugin_admin, 'ft_admin_add_page' );

    $this->loader->add_action ('wp_logout', $plugin_admin, 'destroy_session_on_logout');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    0.0.7
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new FISIF_Tools_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

    $this->loader->add_action ('wp_logout', $plugin_public, 'destroy_session_on_logout');

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    0.0.7
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     0.0.7
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     0.0.7
	 * @return    FISIF_Tools_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     0.0.7
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Configure shortcodes
	 *
	 * @since     0.0.7
	 */
    private function setup_shortcode() {

        // Print Options (link)
        add_shortcode( 'ft:print_options',
                     array(
                         new FISIF_Tools_Pdf($this->get_plugin_name(), $this->get_version() )
                         ,'print_options' ) );

        // PDF Options (link)
        add_shortcode( 'ft:pdf_options',
                      array(
                          new FISIF_Tools_Pdf($this->get_plugin_name(), $this->get_version() )
                          ,'pdf_options' ) );

        // Member Home View
        add_shortcode( 'ft:member_account_summary',
                       array(
                           new FT_Member_Home($this->get_plugin_name(), $this->get_version() )
                           ,'memberSummary' ) );

        // Member - Account Receivable Report View
        add_shortcode( 'ft:member_account_receivable_report',
                       array(
                           new FT_Account_Receivable_Report($this->get_plugin_name(), $this->get_version() )
                           ,'arreport' ) );

        // Member - Payroll Report View
        add_shortcode( 'ft:member_payroll_report',
                      array(
                          new FT_Payroll_Report($this->get_plugin_name(), $this->get_version() )
                          ,'payrollReport' ) );

        // Member PRR Link
        add_shortcode( 'ft:member_payroll_report_link',
                       array(
                           new FT_Payroll_Report($this->get_plugin_name(), $this->get_version() )
                           ,'payrollReportLink' ) );

        // Member - Premium Computation View
        add_shortcode( 'ft:member_premium_computation',
                       array(
                           new FT_Premium_Computation($this->get_plugin_name(), $this->get_version() )
                           ,'premiumComputation' ) );

        // Member - Loss Run Report View
        add_shortcode( 'ft:member_loss_run_report',
                       array(
                           new FT_Loss_Run_Report($this->get_plugin_name(), $this->get_version() )
                           ,'lossRunReport' ) );

         // Member - Loss Ratio Report View
         add_shortcode( 'ft:member_loss_ratio_report',
                        array(
                            new FT_Loss_Ratio_Report($this->get_plugin_name(), $this->get_version() )
                            ,'lossRatioReport' ) );

        // Member - Certificate of Insurance
         add_shortcode( 'ft:member_certificate_of_insurance',
                        array(
                            new FT_Certificate_Of_Insurance($this->get_plugin_name(), $this->get_version() )
                            ,'certificateOfInsurance' ) );

        // Member - Agent Search View
        add_shortcode( 'ft:agent_search',
                       array(
                           new FT_Agent_Search($this->get_plugin_name(), $this->get_version() )
                           ,'agentSearch' ) );

        // Agent Home View
        add_shortcode( 'ft:agent_account_summary',
                       array(
                           new FT_Agent_Home($this->get_plugin_name(), $this->get_version() )
                           ,'agentSummary' ) );

        // Agent Commission Report View
        add_shortcode( 'ft:agent_commission_report',
                       array(
                           new FT_Agent_Commission_Report($this->get_plugin_name(), $this->get_version() )
                           ,'agentCommissionReport' ) );

        // Agent Premium Estimator
        add_shortcode( 'ft:agent_premium_estimator',
                       array(
                           new FT_Agent_Premium_Estimator($this->get_plugin_name(), $this->get_version() )
                           ,'premiumEstimator' ) );

    }
}
?>
