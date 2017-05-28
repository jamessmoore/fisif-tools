<?php

class FT_Account_Receivable_Report extends FISIF_Tools_Public {

	public function __construct() {

	}

	/**
     * Display the Members - Account Receivable Report
     *
     * @since    0.0.7
     * @access   public
     */
    public function arreport() {

        $user = wp_get_current_user();
        if (isset ($_SESSION['fisifmemberid']) && (in_array( 'agent', (array) $user->roles )) ) {
            $BilledID = $_SESSION['fisifmemberid'];
        } else {
            $BilledID = $user->user_login;
        }

        $xx = new FISIF_Tools_Db();

        // grab the associated member data
        $member = $xx->getUserInfo('member', $BilledID);

        // Get the Members AR Totals
        $tablename = "ar_main";
        $params = "WHERE `BilledID`='$BilledID' ";
        $ret = $xx->GetRecords("*", $tablename, $params);
        $member_billed = $ret[0];

        // Get the Agency
        $tablename = "agencies";
        $params = "WHERE `id`='$member_billed[AgencyID]' ";
        $ret = $xx->GetRecords("*", $tablename, $params);
        $agency = $ret[0];

        // Get the FundYears for this Member
        $tablename = "ar_fundyears";
        $params = "WHERE `BilledID`='$BilledID' ";
        $fundyears = $xx->GetRecords("*", $tablename, $params);

        $the_date = date('F d, Y');

				// Define inline styles for report elements
				// This is required as referenced CSS will not control the PDF output for printing
				$div_style = "background-color: #FFFFFF; padding: 0; font: normal 400 10px/1 'open_sansregular', Arial, sans-serif;";
				$table_style = "padding: 0; margin: 0; width: 100%;";
				$tablelabel_style = "background: none repeat scroll 0 0 #898989; background-color: #898989; ";
				$tablelabel_style .= "color: #FFFFFF; font: normal 400 12px/1 'times_new_roman', 'open_sansregular', Arial, sans-serif;";
				$tablelabel_style .= "padding: 5px; margin: 0;";
				$tr_style = "padding: 2px 0;";
				$td_style = "padding: 2px; font: normal 400 8px/1 'times_new_roman', 'open_sansregular', Arial, sans-serif;";
				$th_style = "background-color: #353535; ";
				$th_style .= "color: #FFFFFF; font: normal 400 8px/1 'times_new_roman', 'open_sansregular', Arial, sans-serif;";
				$th_style .= " padding: 2px; text-transform: uppercase;";
				$p_style = "padding: 2px; font: normal 400 8px/1 'times_new_roman', 'open_sansregular', Arial, sans-serif;";
				$rh_td_style = "padding: 10px 20px; font: normal 600 11px/1.6 'times_new_roman', 'open_sansregular', Arial, sans-serif;";

				// place output in buffer
				ob_start();

				// table header row for this report
				$th_row =  "<tr style=\"$tr_style background-color: #353535; \">\n";
						$th_row .= "<th style=\"$th_style\" align=\"left\">Date</th>\n";
						$th_row .= "<th style=\"$th_style\" align=\"left\">Invoice #</th>\n";
						$th_row .= "<th style=\"$th_style\" align=\"left\">Comment</th>\n";
						$th_row .= "<th style=\"$th_style\">TP</th>\n";
						$th_row .= "<th style=\"$th_style\" align=\"right\">Charges</th>\n";
						$th_row .= "<th style=\"$th_style\" align=\"right\">Payments</th>\n";
						$th_row .= "<th style=\"$th_style\" align=\"right\">Other</th>\n";
						$th_row .= "<th style=\"$th_style\" align=\"right\">Balance</th>\n";
				$th_row .= "</tr>\n";

				// horizontal table row seperator
				$hr_row = "<tr style=\"$tr_style\">\n";
						$hr_row .= "<td colspan=\"8\" align=\"center\"><hr /></td>\n";
				$hr_row .= "</tr>\n";

				// empty table row seperator
				$empty_row = "<tr style=\"$tr_style\">\n";
						$empty_row .= "<td style=\"$td_style\" colspan=\"8\" align=\"center\">&nbsp;</td>\n";
				$empty_row .= "</tr>\n";

        echo "<div style=\"$div_style\" class=\"section-fisif-report\">\n";
        echo "<h4 style=\"text-align: center; margin: 0; padding: 0; text-transform: uppercase;\">Food Industry Insurance Fund of New Mexico</h4>\n";
        echo "<p style=\"text-align: center; margin: 0; padding: 0; font-weight: 900;\">$the_date</p>\n";
        echo "<h1 style=\"text-align: left; margin: 0; margin-top: 10px; padding: 10px; border-bottom: 2px solid #000000;\">Account Receivable Report</h1>\n";

        echo "<table class=\"report-header\" width=\"100%\" style=\"$table_style\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr style=\"$tr_style\">\n";
        echo "<td style=\"$rh_td_style\" class=\"report-header-left\" width=\"50%\">$member[name]<br />$member[address]<br />$member[address2]<br />$member[city], $member[state] $member[zip]<br />$member[phone] - Fax: $member[fax]\n";
				echo "</td>\n";
				if (!empty($agency)) {
					echo "<td style=\"$rh_td_style\" class=\"report-header-right\" width=\"50%\">$agency[name]<br />$agency[address]<br />$agency[address2]<br />$agency[city], $agency[state] $agency[zip]<br />$agency[phone] - Fax: $agency[fax]</td>\n";
				}
        echo "</tr></table>\n";
				echo "<p style=\"$p_style padding: 0 20px; text-align: left;\">Member from $member_billed[StartDate]</p>\n";

		echo "<table width=\"100%\" style=\"$table_style\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">";

		// Get the Claims for each FundYear
		foreach ($fundyears as $fundyear) {
			echo "<tr style=\"$tr_style\">\n";
	          echo "<td style=\"$td_style padding: 0;\" align=\"left\" colspan=\"8\">\n";
						echo "<h3 style=\"$tablelabel_style\">Fund Year: $fundyear[YearCode]</h3>\n";
						echo "</td>\n";
	          // echo "<td colspan=\"8\">".print_r($fundyear)."</b></td>\n";
			echo "</tr>\n";
			echo $th_row;

			$tablename = "ar_transactions";
			$params = "WHERE `BilledID`='$BilledID' AND `YearCode`='$fundyear[YearCode]' ";
			$transactions = $xx->GetRecords("*", $tablename, $params);

			foreach ($transactions as $key => $trans) {
				echo "<tr style=\"$tr_style\">\n";
                echo "<td style=\"$td_style\" align=\"left\">$trans[TransDate]</td>\n";
                echo "<td style=\"$td_style\" align=\"left\">$trans[Invoice]</td>\n";
                if ($trans['Payments'] == 0) {
                    echo "<td style=\"$td_style\" align=\"left\">$trans[Comment]</td>\n";
                } else {
                    echo "<td style=\"$td_style\" align=\"center\">$trans[Comment]</td>\n";
                }
                echo "<td style=\"$td_style\" align=\"left\">$trans[TransType]</td>\n";
                echo "<td style=\"$td_style\" align=\"right\">\$".number_format($trans[Charges], 2)."</td>\n";
                echo "<td style=\"$td_style\" align=\"right\">\$".number_format($trans[Payments], 2)."</td>\n";
                echo "<td style=\"$td_style\" align=\"right\">\$".number_format($trans[Other], 2)."</td>\n";
                echo "<td style=\"$td_style\" align=\"right\">\$".number_format($trans[Balance], 2)."</td>\n";
			}
			echo $empty_row;
			echo "<tr style=\"$tr_style\" class=\"section-totals\">\n";
            echo "<td style=\"$td_style\" align=\"left\" colspan=\"4\"><b>Totals for $fundyear[YearCode] ($fundyear[YearTrans] total transactions) </b></td>\n";
            echo "<td style=\"$td_style\" align=\"right\"><b>\$".number_format($fundyear[YearCharges], 2)."</b></td>\n";
            echo "<td style=\"$td_style\" align=\"right\"><b>\$".number_format($fundyear[YearPayments], 2)."</b></td>\n";
            echo "<td style=\"$td_style\" align=\"right\"><b>\$".number_format($fundyear[YearOther], 2)."</b></td>\n";
            echo "<td style=\"$td_style\" align=\"right\"><b>\$".number_format($fundyear[YearBalance], 2)."</b></td>\n";
			echo "</tr>\n";
			echo $empty_row;
		}
		echo $empty_row;
    echo $hr_row;
    echo "<tr style=\"$tr_style\" class=\"section-totals\">\n";
    echo "<td style=\"$td_style\" align=\"left\" colspan=\"4\"><b> Grand Totals  ($member_billed[MemberTrans] total transactions)</b></td>\n";
    echo "<td style=\"$td_style\" align=\"right\"><b>\$".number_format($member_billed[MemberCharges], 2)."</b></td>\n";
    echo "<td style=\"$td_style\" align=\"right\"><b>\$".number_format($member_billed[MemberPayments], 2)."</b></td>\n";
    echo "<td style=\"$td_style\" align=\"right\"><b>\$".number_format($member_billed[MemberOther], 2)."</b></td>\n";
    echo "<td style=\"$td_style\" align=\"right\"><b>\$".number_format($member_billed[MemberBalance], 2)."</b></td>\n";
    echo "</tr>\n";
		echo $empty_row;
    echo "</table>\n";
    echo "</div>\n"; // End of section-fisif-report

		$html_output = ob_get_clean();
		$report_fn = 'ar-report-'.$BilledID;
		$report_title = 'Account Receivable Report';
		$report_orientation = 'portrait';

		$_SESSION['fisiftools'] = '';
		$_SESSION['fisiftools']['report_html'] = $html_output;
		$_SESSION['fisiftools']['report_fn'] = $report_fn;
		$_SESSION['fisiftools']['report_title'] = $report_title;
		$_SESSION['fisiftools']['report_orientation'] = $report_orientation;

		return $html_output;
    }

}
