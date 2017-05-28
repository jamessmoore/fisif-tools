<?php

class FT_Loss_Ratio_Report {

	public function __construct() {

	}

  function lossRatioReport() {

      $user = wp_get_current_user();
      if (isset ($_SESSION['fisifmemberid']) && (in_array( 'agent', (array) $user->roles )) ) {
          $BilledID = $_SESSION['fisifmemberid'];
      } else {
          $BilledID = $user->user_login;
      }

			$xx = new FISIF_Tools_Db();

      // grab the associated member data
      $member = $xx->getUserInfo('member', $BilledID);

      // Get the Members LossRun Totals
      $tablename = "lossruns_main";
      $params = "WHERE `BilledID`='$BilledID' ";
      $result = $xx->GetRecords("*", $tablename, $params);
      $member_billed = $result[0];

			// Get the Agency
			$tablename = "agencies";
			$params = "WHERE `id`='$member[agencyid]' ";
			$ret = $xx->GetRecords("*", $tablename, $params);
			$agency = $ret[0];

      // Get the FundYears for this Member
      $tablename = "lossruns_fundyears";
      $minusFive = date('Y') - 5;
      $params = "WHERE `BilledID`='$BilledID' AND `YearCode` > $minusFive ORDER BY `YearCode` DESC";
      $fundyears = $xx->GetRecords("*", $tablename, $params);

      $the_date = date('F d, Y');

			// Define inline styles for report elements
			// This is required as referenced CSS will not control the PDF output for printing
			$div_style = "background-color: #FFFFFF; margin: 0; padding: 20px; font: normal 400 10px/1 'open_sansregular', Arial, sans-serif;";
			$table_style = "padding: 0; margin: 0; width: 100%;";
			$tablelabel_style = "background: none repeat scroll 0 0 #898989; background-color: #898989; ";
			$tablelabel_style .= "color: #FFFFFF; font: normal 400 12px/1 'times_new_roman', 'open_sansregular', Arial, sans-serif;";
			$tablelabel_style .= "padding: 5px; margin: 0; margin-top: 20px; text-transform: uppercase;";
			$tr_style = "padding: 2px 0;";
			$td_style = "padding: 4px; font: normal 400 9px/1 'times_new_roman', 'open_sansregular', Arial, sans-serif;";
			$th_style = "background: none repeat scroll 0 0 #353535; border: 1px solid #353535; background-color: #353535; ";
			$th_style .= "color: #FFFFFF; font: normal 400 9px/1 'times_new_roman', 'open_sansregular', Arial, sans-serif;";
			$th_style .= " padding: 2px; text-transform: uppercase;";
			$p_style = "padding: 2px; font: normal 400 9px/1 'times_new_roman', 'open_sansregular', Arial, sans-serif;";
			$rh_td_style = "padding: 10px 20px; font: normal 600 10px/1 'times_new_roman', 'open_sansregular', Arial, sans-serif;";

			// place output in buffer
			ob_start();

			// table header row for this report
			$th_row =  "<tr style=\"$tr_style\">\n";
					$th_row .= "<th style=\"$th_style\">Member</th>\n";
					$th_row .= "<th style=\"$th_style\">Fund Year </th>\n";
					$th_row .= "<th style=\"$th_style\">Premium</th>\n";
					$th_row .= "<th style=\"$th_style\">Claim Paid</th>\n";
					$th_row .= "<th style=\"$th_style\">Reserved</th>\n";
					$th_row .= "<th style=\"$th_style\">Total</th>\n";
					$th_row .= "<th style=\"$th_style text-align: right;\">Loss Ratio</th>\n";;
			$th_row .= "</tr>\n";

			// horizontal table row seperator
			$hr_row = "<tr style=\"$tr_style\">\n";
					$hr_row .= "<td style=\"$td_style\"colspan=\"7\" align=\"center\"><hr></td>\n";
			$hr_row .= "</tr>\n";

			// empty table row seperator
			$empty_row = "<tr style=\"$tr_style\">\n"; 
					$empty_row .= "<td colspan=\"7\" align=\"center\">&nbsp;</td>\n";
			$empty_row .= "</tr>\n";

      echo "<div style=\"$div_style\" class=\"section-fisif-report\">\n";
			echo "<h3 align=\"center\" style=\"text-align: center;  text-transform: uppercase; padding: 10px; margin: 0;\">Food Industry Insurance Fund of New Mexico</h3>\n";
			echo "<p style=\"text-align: center; margin: 0; padding: 0; font-weight: 900;\">P.O. Box 14710</p>\n";
			echo "<p style=\"text-align: center; margin: 0; padding: 0; font-weight: 900;\">Albuquerque, NM 87191-4710</p>\n";
			echo "<p style=\"text-align: center; margin: 0; margin-top: 20px; padding: 0; font-weight: 900;\">$the_date</p>\n";
			echo "<h1 style=\"text-align: left; margin: 0; margin-top: 10px; margin-left: 10px; padding: 10px; border-bottom: 2px solid #000000;\">Loss Ratio Report</h1>\n";
			echo "<table class=\"report-header\" width=\"100%\" style=\"$table_style\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr>\n";
			echo "<td style=\"$rh_td_style\" class=\"report-header-left\">$member[id]<br />$member[name]<br />$member[address]<br />$member[address2]<br />$member[city], $member[state] $member[zip]<br />$member[phone] - Fax: $member[fax]</td>\n";
			echo "<td style=\"$rh_td_style\" class=\"report-header-right\">\n";
			if ($agency){
				echo "$agency[name]<br />$agency[address]<br />$agency[address2]<br />$agency[city], $agency[state] $agency[zip]<br />";
				if (!empty($agency[phone])) {
					echo $agency['phone']." - ";
				} elseif (!empty($agency['fax'])) {
					echo "Fax: ".$agency['fax'];
				}
			} else {
				var_dump($member);
			}
			echo "</td>\n";
			echo "</tr></table>\n";

			echo "<table width=\"100%\" style=\"$table_style\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">";
			echo $th_row;

	// Get the Claims for each FundYear
	foreach ($fundyears as $fundyear) {

		$tablename = "lossruns_claims";
		$params = "WHERE `BilledID`='$BilledID' AND `YearCode`='$fundyear[YearCode]' ";
		$claims = $xx->GetRecords("*", $tablename, $params);

		echo "<tr style=\"$tr_style\">\n";
		echo "<td style=\"$td_style text-align: center;\">".$BilledID."</td>\n";
		echo "<td style=\"$td_style text-align: center;\">".$fundyear[YearCode]."</td>\n";
		echo "<td style=\"$td_style\">\$".number_format($fundyear[FY_Premium], 2)."</td>\n";
		$claimPaid = $fundyear[FY_IndPaid] + $fundyear[FY_MedPaid] + $fundyear[FY_LegPaid] + $fundyear[FY_OthPaid];
		echo "<td style=\"$td_style\">\$".number_format($claimPaid, 2)."</td>\n";
		$reserved = $fundyear[FY_IndReserves] + $fundyear[FY_MedReserves] + $fundyear[FY_LegReserves] + $fundyear[FY_OthReserves];
		echo "<td style=\"$td_style\">\$".number_format($reserved, 2)."</td>\n";
		$total = $claimPaid + $reserved;
		echo "<td style=\"$td_style\">\$".number_format($total, 2)."</td>\n";
		echo "<td style=\"$td_style text-align: right;\">".number_format($fundyear[FY_LossRatio], 2)."</td>\n";
		echo "</tr>\n";
	}
	echo $hr_row;
  echo "</table>\n";
  echo "</div>\n"; // end fisif-report

			$html_output = ob_get_clean();
			$report_fn = 'lossrun-ratio-'.$BilledID;
			$report_title = 'Loss Ratio Report';
			$report_orientation = 'portrait';

			//$_SESSION['fisiftools'] = '';
			$_SESSION['fisiftools']['report_html'] = $html_output;
			$_SESSION['fisiftools']['report_fn'] = $report_fn;
			$_SESSION['fisiftools']['report_title'] = $report_title;
			$_SESSION['fisiftools']['report_orientation'] = $report_orientation;

			return $html_output;

  }
}
