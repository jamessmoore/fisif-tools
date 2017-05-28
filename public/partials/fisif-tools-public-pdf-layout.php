<?php

if(!function_exists('create_pdf_layout')) {

	function create_pdf_layout($report_title, $report_fn, $report_html) {
		global $report_title, $report_fn, $report_html, $pdf_export_css_file;

		ob_start();

		// HEADER
		echo '<!DOCTYPE html><html xmlns="http://www.w3.org/1999/xhtml" lang="en-US"><head><title>'.$report_title.'</title>';
		echo '<link rel="stylesheet" type="text/css" href="'.$pdf_export_css_file.'" />';
		echo '</head><body>';
		// CONTENT
		echo '<div class="main_div">';
					echo $report_html;
		echo "</div>";

		// FOOTER
		echo "</body></html>";

		return ob_get_clean();
	}

}
?>
