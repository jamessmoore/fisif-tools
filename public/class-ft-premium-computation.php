<?php

class FT_Premium_Computation extends FISIF_Tools_Public {

	public function __construct() {

	}

  function premiumComputation() {

      $user = wp_get_current_user();
      if (isset ($_SESSION['fisifmemberid']) && (in_array( 'agent', (array) $user->roles )) ) {
          // $user is an agent
          $BilledID = $_SESSION['fisifmemberid'];
      } else {
          // $user is assumed to be a member
          $BilledID = $user->user_login;
      }

			$xx = new FISIF_Tools_Db();

      // grab the associated member data
      $member = $xx->getUserInfo('member', $BilledID);

      // Get the Members AR Totals
      $tablename = "pc_main";
      $params = "WHERE `BilledID`='$BilledID' ";
      $result = $xx->GetRecords("*", $tablename, $params);
      $member_billed = $result[0];

      // Get the Agency
      $tablename = "agencies";
      $params = "WHERE `id`='$member_billed[AgencyID]' ";
      $result = $xx->GetRecords("*", $tablename, $params);
      $agency = $result[0];

      // Default to FISIF if no agency
      if (empty($agency)) {
          $agency['name'] = "FISIF";
          $agency['address'] = "P.O. Box 14710";
          $agency['city'] = "Albuquerque";
          $agency['zip'] = "87191-4710";
      }

      // Get the Classcodes for this Member
      $tablename = "pc_classcodes";
      $params = "WHERE `BilledID`='$BilledID' ";
      $classcodes = $xx->GetRecords("*", $tablename, $params);

      // Get the Discounts for this Member
      $tablename = "pc_discounts";
      $params = "WHERE `BilledID`='$BilledID' ";
      $discounts = $xx->GetRecords("*", $tablename, $params);

      $the_date = date('F d, Y');

			// Define inline styles for report elements
			// This is required as referenced CSS will not control the PDF output for printing
			$div_style = "background-color: #FFFFFF; padding: 0; font: normal 400 10px/1 'open_sansregular', Arial, sans-serif;";
			$table_style = "padding: 0; margin: 0; width: 100%;";
			$tablelabel_style = "background: none repeat scroll 0 0 #898989; background-color: #898989; ";
			$tablelabel_style .= "color: #FFFFFF; font: normal 400 12px/1 'times_new_roman', 'open_sansregular', Arial, sans-serif;";
			$tablelabel_style .= "padding: 5px; margin: 0;";
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
			$th_row =  "<tr style=\"$tr_style background-color: #353535; \">\n";
					$th_row .= "<th style=\"$th_style\">ClassCode</th>\n";
					$th_row .= "<th style=\"$th_style\">Description</th>\n";
					$th_row .= "<th style=\"$th_style\" align=\"right\">Exposure / Payroll</th>\n";
					$th_row .= "<th style=\"$th_style\">Rate</th>\n";
					$th_row .= "<th style=\"$th_style\" align=\"right\"	>Premium</th>\n";
			$th_row .= "</tr>\n";

			// horizontal table row seperator
			$hr_row = "<tr style=\"$tr_style\">\n";
					$hr_row .= "<td style=\"margin: 0; padding: 0;\" colspan=\"8\" align=\"center\"><hr /></td>\n";
			$hr_row .= "</tr>\n";

			// empty table row seperator
			$empty_row = "<tr style=\"$tr_style\">\n";
					$empty_row .= "<td style=\"$td_style\" colspan=\"8\" align=\"center\">&nbsp;</td>\n";
			$empty_row .= "</tr>\n";

      echo "<div style=\"$div_style\" class=\"section-fisif-report\">\n";

      echo "<h3 align=\"center\" style=\"text-align: center;  text-transform: uppercase; padding: 10px; margin: 0;\">Food Industry Insurance Fund of New Mexico</h3>\n";
      echo "<p style=\"text-align: center; margin: 0; padding: 0; font-weight: 900;\">P.O. Box 14710</p>\n";
      echo "<p style=\"text-align: center; margin: 0; padding: 0; font-weight: 900;\">Albuquerque, NM 87191-4710</p>\n";
      echo "<p style=\"text-align: center; margin: 0; padding: 0; font-weight: 900;\">$the_date</p>\n";
			echo "<h1 style=\"text-align: left; margin: 0; margin-top: 10px; margin-left: 10px; padding: 10px; border-bottom: 2px solid #000000;\">Premium Computation</h1>\n";
      echo "<table class=\"report-header\" width=\"100%\" style=\"$table_style\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr>\n";
			echo "<td style=\"$rh_td_style\" class=\"report-header-left\">$member[id]<br />$member[name]<br />$member[address]<br />$member[address2]<br />$member[city], $member[state] $member[zip]<br />$member[phone] - Fax: $member[fax]</td>\n";
			echo "<td style=\"$rh_td_style\" class=\"report-header-right\">$agency[name]<br />$agency[address]<br />$agency[address2]<br />$agency[city], $agency[state] $agency[zip]<br />";
			if (!empty($agency[phone])) {
				echo $agency['phone']." - ";
			} elseif (!empty($agency['fax'])) {
				echo "Fax: ".$agency['fax'];
			}
			echo "</td>\n";
	    echo "</tr></table>\n";
	    echo "<p align=\"center\"><br /><b>$member_billed[YearCode]</b><br />From: $member_billed[StartDate] - To: $member_billed[EndDate]</p>\n";

	    echo "<table width=\"100%\" style=\"$table_style\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">\n";
			echo $th_row;

			foreach ($classcodes as $classcode) {
				echo "<tr style=\"$tr_style\">\n";
	            echo "<td style=\"$td_style\" align=\"left\">$classcode[ClassCode]</td>\n";
	            echo "<td style=\"$td_style\" align=\"left\">$classcode[Description]</td>\n";
	            echo "<td style=\"$td_style\" align=\"right\">\$".number_format($classcode[Exposure], 2)."</td>\n";
	            echo "<td style=\"$td_style\" align=\"center\">$classcode[Rate]</td>\n";
	            echo "<td style=\"$td_style\" align=\"right\">\$".number_format($classcode[Premium], 2)."</td>\n";
				echo "</tr>\n";
			}
			echo $empty_row;
			echo "<tr style=\"$tr_style\">\n";
	        echo "<td style=\"$td_style\" align=\"left\">&nbsp;</td>\n";
	        echo "<td style=\"$td_style\" align=\"right\" colspan=\"2\">Manual Premium Subject to Modifier</td>\n";
	        echo "<td style=\"$td_style\" align=\"right\" colspan=\"2\">\$".number_format($member_billed[ManualPremium], 2)."</td>\n";
			echo "</tr>\n";
			echo "<tr style=\"$tr_style\">\n";
	        echo "<td style=\"$td_style\" align=\"left\">&nbsp;</td>\n";
	        echo "<td style=\"$td_style\" align=\"right\" colspan=\"2\">Experience Modifier</td>\n";
	        echo "<td style=\"$td_style\" align=\"right\" colspan=\"2\">$member_billed[ExMod]</td>\n";
			echo "</tr>\n";
			echo "<tr style=\"$tr_style\">\n";
	        echo "<td style=\"$td_style\" align=\"left\" colspan=\"4\">&nbsp;</td>\n";
	        echo "<td style=\"margin:0; padding: 0;\" align=\"left\"><hr style=\"margin: 0; padding: 0;\" /></td>\n";
			echo "</tr>\n";
			echo "<tr style=\"$tr_style\">\n";
	        echo "<td style=\"$td_style\" align=\"left\">&nbsp;</td>\n";
	        echo "<td style=\"$td_style\" align=\"right\" colspan=\"2\">Total Standard Premium</td>\n";
	        echo "<td style=\"$td_style\" align=\"right\" colspan=\"2\">\$".number_format($member_billed[StandardPremium], 2)."</td>\n";
			echo "</tr>\n";
			echo "<tr style=\"$tr_style\">\n";
	        echo "<td style=\"$td_style\" align=\"left\" colspan=\"4\">&nbsp;</td>\n";
	        echo "<td style=\"$td_style margin:0; padding: 0;\" align=\"left\"><hr style=\"margin: 0; padding: 0;\" /></td>\n";
			echo "</tr>\n";
			if (!empty($discounts) || !empty($member_billed['VolumeDiscount'])) {
				foreach ($discounts as $discount) {
					echo "<tr style=\"$tr_style\">\n";
	                echo "<td style=\"$td_style\" align=\"left\">&nbsp;</td>\n";
	                echo "<td style=\"$td_style\" align=\"right\" colspan=\"2\">$discount[Discount]</td>\n";
	                echo "<td style=\"$td_style\" align=\"right\" colspan=\"2\">(\$".number_format($discount[DiscountAmount], 2).")</td>\n";
					echo "</tr>\n";
				}
				if (!empty($member_billed['VolumeDiscount'])) {
					echo "<tr style=\"$tr_style\">\n";
	                echo "<td style=\"$td_style\" align=\"left\">&nbsp;</td>\n";
	                echo "<td style=\"$td_style\" align=\"right\" colspan=\"2\">Less Volume Discount ($member_billed[VolumeDiscountRate]%)</td>\n";
	                echo "<td style=\"$td_style\" align=\"right\" colspan=\"2\">(\$".number_format($member_billed[VolumeDiscount], 2).")</td>\n";
					echo "</tr>\n";
				}
				echo "<tr style=\"$tr_style\">\n";
	            echo "<td style=\"$td_style\" align=\"left\" colspan=\"4\">&nbsp;</td>\n";
	            echo "<td style=\"$td_style\" align=\"left\"><hr style=\"margin: 0; padding: 0;\" /></td>\n";
				echo "</tr>\n";
			}
			echo "<tr style=\"$tr_style\">\n";
	        echo "<td style=\"$td_style\" align=\"left\">&nbsp;</td>\n";
	        echo "<td style=\"$td_style\" align=\"right\" colspan=\"2\">Total</td>\n";
	        echo "<td style=\"$td_style\" align=\"right\" colspan=\"2\">\$<b>".number_format($member_billed[Total], 2)."</b></td>\n";
			echo "</tr>\n";
			echo "<tr style=\"$tr_style\">\n";
	        echo "<td style=\"$td_style\" align=\"left\" colspan=\"4\">&nbsp;</td>\n";
	        echo "<td style=\"$td_style margin:0; padding: 0;\" align=\"left\"><hr style=\"margin: 0; padding: 0;\" /></td>\n";
			echo "</tr>\n";
			echo "<tr style=\"$tr_style\">\n";
	        echo "<td style=\"$td_style\" align=\"left\">&nbsp;</td>\n";
	        echo "<td style=\"$td_style\" align=\"right\" colspan=\"2\">Plus Administration Charges</td>\n";
	        echo "<td style=\"$td_style\" align=\"right\" colspan=\"2\">\$".number_format($member_billed[AdminCharges], 2)."</td>\n";
			echo "</tr>\n";
			echo "<tr style=\"$tr_style\">\n";
	        echo "<td style=\"$td_style\" align=\"left\" colspan=\"4\">&nbsp;</td>\n";
	        echo "<td style=\"$td_style margin:0; padding: 0;\" align=\"left\"><hr style=\"margin: 0; padding: 0;\" /></td>\n";
			echo "</tr>\n";
			echo "<tr style=\"$tr_style\">\n";
	        echo "<td style=\"$td_style\" align=\"left\">&nbsp;</td>\n";
	        echo "<td style=\"$td_style\" align=\"right\" colspan=\"2\"><b>Total Normal Premium</b></td>\n";
	        echo "<td style=\"$td_style\" align=\"right\" colspan=\"2\"><b>\$".number_format($member_billed[GrandTotal], 2)."</b></td>\n";
			echo "</tr>\n";
			echo "<tr style=\"$tr_style\">\n";
	        echo "<td style=\"$td_style\" align=\"left\" colspan=\"5\">&nbsp;</td>\n";
			echo "</tr>\n";
      echo "</table>\n";

      echo "</div>\n";

			$html_output = ob_get_clean();
			$report_fn = 'premium-computation-'.$BilledID;
			$report_title = 'Premium Computation Report';
			$report_orientation = 'portrait';

			$_SESSION['fisiftools'] = '';
			$_SESSION['fisiftools']['report_html'] = $html_output;
			$_SESSION['fisiftools']['report_fn'] = $report_fn;
			$_SESSION['fisiftools']['report_title'] = $report_title;
			$_SESSION['fisiftools']['report_orientation'] = $report_orientation;

			return $html_output;
  }
}
