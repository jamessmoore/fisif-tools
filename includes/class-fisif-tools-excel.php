<?php
/**
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    FISIF_Tools
 * @subpackage FISIF_Tools/includes
 * @author     James S. Moore <james@teamweb.us>
 */
use Dompdf\Dompdf;

class FISIF_Tools_Excel {

    function FISIF_Tools_Excel() {		// Constructor

    }

    public function excel_options(){


    }

    public function export_options(){

      $proto = 'http';
      if ($_SERVER['HTTPS']) {
        $proto = 'https';
      }
      $print_url = $proto.'://'. $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . '?export=excel';

      //$print_opt_html = '<div class="print-options"><p>';
        //$print_opt_html .= '<a href="'.$proto.'://'. $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"].'?export=ftpdf&force">Print / View as PDF</a>';
        $print_opt_html = "<input class=\"formbutton\" type=\"button\"";
          $print_opt_html .= " name=\"print\" value=\"Print This Page\"";
          $print_opt_html .= " onClick=\"window.location='$print_url'\">\n";
      //$print_opt_html .= '</p></div>';

      return $print_opt_html;
    }

    public function excel_export_process(){

        /*ini_set('display_errors', 1);
          ini_set('display_startup_errors', 1);
          error_reporting(E_ERROR | E_PARSE);*/

        //?export=ft-pdf&num=3&post_type=post&force
        //?export=ft-pdf&post_id=3&post_type=post&force

        global $pdf_export_post_type, $pdf_export_force, $pdf_posts_per_page, $pdf_export_post_id, $pdf_export_final_pdf;
        global $report_title, $report_fn, $report_html, $report_orientation;

        $pdf_export_post_type = isset($_REQUEST['post_type']) && $_REQUEST['post_type'] != '' ? $_REQUEST['post_type'] : 'post';
        $pdf_export_post_id = isset($_REQUEST['post_id']) ? $_REQUEST['post_id'] : '';
        $pdf_posts_per_page = isset($_REQUEST['num']) ? $_REQUEST['num'] : -1;
        $pdf_export_check = isset($_REQUEST['export']) ? $_REQUEST['export'] : '';
        $pdf_export_force = isset($_REQUEST['force']);

        $pdf_export_final_pdf = FT_PDF_EXPORTDIR.$pdf_export_post_type.FT_PDF_EXPORTER_EXTRA_FILE_NAME.date('dMY').'.pdf';

        if ($pdf_export_check == 'print') {

          if ($_SESSION['fisiftools']['report_html']) {
            $report_html = $_SESSION['fisiftools']['report_html'];
          }

          // add WordPress header elements to return html
          $html_header = "<!DOCTYPE html>\n";
          $html_header .= "<html>\n";
          $html_header .= "<head>\n";
          $html_header .= "<meta charset=\"".get_bloginfo( 'charset' )."\" />\n";
          // $html_header .= "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no\"/>\n";
          //$html_header .= "<link rel=\"pingback\" href=\"".bloginfo('pingback_url').">\n";
          //$html_header .= wp_head();
          //$html_header .= "<link rel='stylesheet' id='causes-style-css' href='http://fisif-demo.ohmydocker.com:7771/wp-content/themes/fisif-2016/style.css?ver=4.5.3' type='text/css' media='all' />";
          $html_header .= "</head>\n";
          $html_header .= "<body onload=\"window.print();\">\n";
          $html_header .= "<div id=\"wrapper\" class=\"wrapper\">\n";

          // add WordPress footer elements to return html
          $html_footer = "</div>\n";
          //$html_footer .= wp_footer();
          $html_footer .= "</body>\n";
          $html_footer .= "</html>\n";

          $packed_html = $html_header . $report_html . $html_footer;

          echo $packed_html;

          exit();
        }
        elseif ($pdf_export_check == 'ftpdf') {

            if ($pdf_export_force || date("dMY", filemtime($pdf_export_final_pdf)) != date('dMY')) {
                // self::create_pagenumber_merge();
              }

            $filename = $pdf_export_post_type.FT_PDF_EXPORTER_EXTRA_FILE_NAME.date('dMY').'.pdf';

            header('Content-type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . filesize($pdf_export_final_pdf));
            header('Accept-Ranges: bytes');
            readfile($pdf_export_final_pdf);

            exit();
        }
    }

}
?>
