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

class FISIF_Tools_Pdf {

    function FISIF_Tools_Pdf() {		// Constructor

    }

    public function pdf_options(){

      $proto = 'http';
      if ($_SERVER['HTTPS']) {
        $proto = 'https';
      }
      $pdf_url = $proto.'://'. $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . '?export=ftpdf&force';

      //$print_opt_html = '<div class="print-options"><p>';
        //$print_opt_html .= '<a href="'.$proto.'://'. $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"].'?export=ftpdf&force">Print / View as PDF</a>';
        $pdf_opt_html = "<input class=\"formbutton\" type=\"button\"";
          $pdf_opt_html .= " name=\"print\" value=\"Generate PDF / Print\"";
          $pdf_opt_html .= " onClick=\"window.location='$pdf_url'\">\n";
      //$print_opt_html .= '</p></div>';

      return $pdf_opt_html;
    }

    public function print_options(){

      $proto = 'http';
      if ($_SERVER['HTTPS']) {
        $proto = 'https';
      }
      $print_url = $proto.'://'. $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . '?export=print';

      //$print_opt_html = '<div class="print-options"><p>';
        //$print_opt_html .= '<a href="'.$proto.'://'. $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"].'?export=ftpdf&force">Print / View as PDF</a>';
        $print_opt_html = "<input class=\"formbutton\" type=\"button\"";
          $print_opt_html .= " name=\"print\" value=\"Print This Page\"";
          $print_opt_html .= " onClick=\"window.location='$print_url'\">\n";
      //$print_opt_html .= '</p></div>';

      return $print_opt_html;
    }

    public function pdf_export_process(){

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
                self::create_pagenumber_merge();
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

		public function create_pagenumber_merge() {

			global $pdf_export_post_type, $pdf_export_post_id, $pdf_posts_per_page, $pdf_export_force, $pdf_export_final_pdf;
      global $report_title, $report_fn, $report_html;

			// Get post type in case the post_id parameter was used, but not the post_type one
			if($pdf_export_post_id != '' && $pdf_export_post_type = 'post') {
				$pdf_export_post_type = get_post_type($pdf_export_post_id);
			}

			// Merge Settings
			$pdf = new DC_Rate_Plan_Pdf_All_PDFMerger;

			// Page Number Settings
			if(FT_PDF_PAGINATION && $pdf_export_post_id == '' && $pdf_posts_per_page > 1) {
				$pdf2 = new PAGENO;
				$pno = 1;
				$page_offset = 0;
				function add_page_no($pdf_export_final_pdf, $newpdfpathname2, $offset=0) {
					$pdf123= new PDF();

				    $pdf123->offset = $offset;
				    $pagecount = $pdf123->setSourceFile($pdf_export_final_pdf);

				    for ($i=1; $i <= $pagecount; $i++) {
				        $tplidx = $pdf123->ImportPage($i);
				        $size = $pdf123->getTemplateSize($tplidx);

				        if ($size['w'] > $size['h']) {
				            $pdf123->AddPage('L', array($size['w'], $size['h']));
				        } else {
				            $pdf123->AddPage('P', array($size['w'], $size['h']));
				        }

				        //$pdf->addPage();
				        $pdf123->useTemplate($tplidx);
				    }

				    $pdf123->Output($newpdfpathname2,"F");

					unset($pdf123);
					gc_collect_cycles();
					return $pagecount;
				}
      }

			// Check for Cached Posts
			//if($pdf_export_force = true)
            //delete_transient('ft_pdf_export_posts');

			//if(get_transient('ft_pdf_export_posts') === false) {

            // The Query
            //if($pdf_export_post_id != '') { // Get a specific Post
            //$pdf_query_args = array(
            //'p'   => $pdf_export_post_id,
            //'post_type' => $pdf_export_post_type
            //);
            //} else {
            //$pdf_query_args = array( // Get all Posts
            //'posts_per_page'   => $pdf_posts_per_page,
            //'post_type' => $pdf_export_post_type,
            //'post_status'      => 'publish',
            //'post_parent'      => 0,
            //);
            //}

            //$pdf_query = new WP_Query( $pdf_query_args );

            //Cache Results - temporarily disabled
            // set_transient('ft_pdf_export_posts', $pdf_query, 23 * HOUR_IN_SECONDS );
      //  }

			//$pdf_query = get_transient('ft_pdf_export_posts');
      // Get the report data from constant
			$report_html = '';
      if ($_SESSION['fisiftools']['report_html']) {
        $report_html = $_SESSION['fisiftools']['report_html'];
      }

        if ($report_html)
        {
            //global $post;
            //setup_postdata($post);

            //$post_id = get_the_ID();
            $report_title = 'Default Report Title';
            $report_fn = 'default-report';

            if ($_SESSION['fisiftools']['report_title']) {
              $report_title = $_SESSION['fisiftools']['report_title'].'-'.date('Ymd-H');
            }
            if ($_SESSION['fisiftools']['report_fn']) {
              $report_fn = $_SESSION['fisiftools']['report_fn'].'-'.date('Ymd-H');
            }

            // add WordPress header/footer elements to return html
            //$html_header = "<!DOCTYPE html>\n";
            //$html_header .= "<html>\n";
            //$html_header .= "<head>\n";
            //$html_header .= "<meta charset=\"".get_bloginfo( 'charset' )."\" />\n";
            //$html_header .= "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no\"/>\n";
            //$html_header .= "<link rel=\"pingback\" href=\"".bloginfo('pingback_url').">\n";
            //$html_header .= wp_head();
            //$html_header .= "<link rel='stylesheet' id='causes-style-css' href='http://fisif-demo.ohmydocker.com:7771/wp-content/themes/fisif-2016/style.css?ver=4.5.3' type='text/css' media='all' />";
            //$html_header .= "</head>\n";
            //$html_header .= "<body>\n";
            //$html_header .= "<div id=\"wrapper\" class=\"wrapper\">\n";

            //$html_footer = "</div>\n";
            //$html_footer .= wp_footer();
            //$html_footer .= "</body>\n";
            //$html_footer .= "</html>\n";

            //$packed_html = $html_header . $report_html . $html_footer;

            //$file_to_save = FT_PDF_EXPORTDIR.'pdf/'.$post_id.'.pdf';
            $file_to_save = FT_PDF_EXPORTDIR.'pdf/'.$report_fn.'.pdf';

            if ($pdf_export_force = true || !file_exists($file_to_save) || date("dMY-H", filemtime($file_to_save)) != date('dMY-H')) {

                // $html = create_pdf_layout($post,$term);
                $output_html = create_pdf_layout($report_title, $report_fn, $report_html);

                // DOMPDF
                $dompdf = new DOMPDF();
                $dompdf->setPaper(DOMPDF_PAPER_SIZE, DOMPDF_PAPER_ORIENTATION);
                $options = $dompdf->getOptions();
                $options->set(array(
                    'isHtml5ParserEnabled' => DOMPDF_ENABLE_HTML5,
                    'isRemoteEnabled' => DOMPDF_ENABLE_REMOTE,
                    'dpi' => DOMPDF_DPI,
                    'isFontSubsettingEnabled' => DOMPDF_ENABLE_FONTSUBSETTING,
                    'defaultMediaType' => DOMPDF_MEDIATYPE,
                    'fontHeightRatio' => DOMPDF_FONTHEIGHTRATIO
                ));
                $dompdf->setOptions($options);

                // $dompdf->load_html(stripslashes(preg_replace('/\s{2,}/', '', $output_html)));
                $dompdf->load_html(stripslashes($output_html));
                $dompdf->render();

                if(FT_PDF_PAGINATION && $pdf_export_post_id == '' && $pdf_posts_per_page > 1) {
                    $file_to_save_temp = $file_to_save.'.temp';
                    //save the temporary pdf file on the server
                    file_put_contents($file_to_save_temp, $dompdf->output());
                    //save final pdf with page number
                    $page_offset += add_page_no($file_to_save_temp, $file_to_save, $page_offset);
                    // Remove temp file
                    unlink($file_to_save_temp);
                } else {
                    //save the pdf file on the server
                    file_put_contents($file_to_save, $dompdf->output());
                }

                // WRITE TO HTML FILES - DEBUG ONLY
                if(FT_PDF_HTML_OUTPUT) {
                    $file_to_save2 = FT_PDF_EXPORTDIR.'html/'.$report_fn.'.html';
                    $myfile = fopen($file_to_save2, "w") or die("Unable to open file!");
                    $txt = stripslashes($output_html);
                    fwrite($myfile, $txt);
                    fclose($myfile);
                }

            }

            if(FT_PDF_PAGINATION && $pdf_export_post_id == '' && $pdf_posts_per_page > 1) {
                // Pagination Stuff
                update_post_meta($post_id, 'pdf_export_page_no', $pno);
                $pagecount = $pdf2->setSourceFile($file_to_save);
                $pno += $pagecount;
            }

            // Create PDF of single post
            // $pdf->addPDF($file_to_save, 'all'); // this saves to filesystem
            $dompdf->stream($file_to_save, array('Attachment'=>'0')); // this opens it directly, in the same window

            wp_reset_postdata(); wp_reset_query();
        }
        else
        {
            wp_die('The post type "'.$pdf_export_post_type.'"" doesn\'t have any data!<br/>Try to add to your URL &post_type=slug-of-post-type and make sure there actually are published reports of that type.', 'FT PDF Exporter');
        }


        $pdf->merge('file', $pdf_export_final_pdf);

    }

}
?>
