<?php

class FT_Loss_Run_Report {

	public function __construct() {

	}

  function lossRunReport() {

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

      // Get the FundYears for this Member
      $tablename = "lossruns_fundyears";
      $minusFive = date('Y') - 5;
      $params = "WHERE `BilledID`='$BilledID' AND `YearCode` > $minusFive ORDER BY `YearCode` DESC";
      $fundyears = $xx->GetRecords("*", $tablename, $params);

      $the_date = date('F d, Y');

			// Define inline styles for report elements
			$div_style = "background-color: #FFFFFF; padding: 0; font: normal 400 10px/1 'open_sansregular', Arial, sans-serif;";
			$table_style = "padding: 0; margin: 0; width: 100%;";
			$tablelabel_style = "background-color: #FFFFFF; border-bottom: 2px solid #000000;";
			$tablelabel_style .= "color: #000000; font: normal 400 16px/1 'times_new_roman', 'open_sansregular', Arial, sans-serif;";
			$tablelabel_style .= "padding: 5px; margin: 0; margin-top: 20px;";
			$tr_style = "padding: 2px 0;";
			$td_style = "padding: 2px; font: normal 400 9px/1 'times_new_roman', 'open_sansregular', Arial, sans-serif;";
			$th_style = "background-color: #FFFFFF; border-bottom: 2px solid #000000;";
			$th_style .= "color: #000000; font: normal 400 9px/1 'times_new_roman', 'open_sansregular', Arial, sans-serif;";
			$th_style .= " padding: 5px 0; text-transform: uppercase;";
			$p_style = "padding: 2px; font: normal 400 8px/1 'times_new_roman', 'open_sansregular', Arial, sans-serif;";

			// table header row for this report
			$th_row =  "<tr style=\"$tr_style\">\n";
					$th_row .= "<th style=\"$th_style\">Memb#&nbsp;/&nbsp;Claim#</th>\n";
					$th_row .= "<th style=\"$th_style\">Member / Claimant</th>\n";
					$th_row .= "<th style=\"$th_style\">Class</th>\n";
					$th_row .= "<th style=\"$th_style\">CI Desc. / Job Title</th>\n";
					$th_row .= "<th style=\"$th_style\">Injury / Closed</th>\n";
					$th_row .= "<th style=\"$th_style\">Accident Info</th>\n";
					$th_row .= "<th style=\"$th_style\">Reserves</th>\n";
					$th_row .= "<th style=\"$th_style\">Paid</th>\n";
					$th_row .= "<th style=\"$th_style\">-- Totals --</th>\n";
			$th_row .= "</tr>\n";

			// horizontal table row seperator
			$hr_row = "<tr style=\"$tr_style\">\n";
					$hr_row .= "<td style=\"$td_style\" colspan=\"9\" align=\"center\"><hr /></td>\n";
			$hr_row .= "</tr>\n";

			// empty table row seperator
			$empty_row = "<tr style=\"$tr_style\">\n";
					$empty_row .= "<td style=\"$td_style\" colspan=\"9\" align=\"center\">&nbsp;</td>\n";
			$empty_row .= "</tr>\n";

			// place output in buffer
			ob_start();

      echo "<div style=\"$div_style\" class=\"section-fisif-report\">\n";
      echo "<h1 style=\"text-align: center; margin: 0; padding: 0; text-transform: uppercase;\">Loss Run Report</h1>\n";
      echo "<h4 style=\"text-align: center; margin: 0; padding: 0; text-transform: uppercase;\">Food Industry Insurance Fund of New Mexico</h4>\n";
      echo "<p style=\"text-align: center; margin: 0; padding: 0; font-weight: 900;\">$the_date</p>\n";

			echo "<table style=\"$table_style\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">\n";

	// Get the Claims for each FundYear
	foreach ($fundyears as $fundyear) {

		echo "<tr style=\"$tr_style\">\n";
					echo "<td style=\"$td_style padding: 0;\" align=\"left\" colspan=\"9\">\n";
					echo "<h3 style=\"$tablelabel_style\">Fund Year: $fundyear[YearCode]</h3>\n";
					echo "</td>\n";
					// echo "<td colspan=\"8\">".print_r($fundyear)."</b></td>\n";
		echo "</tr>\n";

		$tablename = "lossruns_claims";
		$params = "WHERE `BilledID`='$BilledID' AND `YearCode`='$fundyear[YearCode]' ";
		$claims = $xx->GetRecords("*", $tablename, $params);

		if ($claims){
			echo $th_row;
		}

		foreach ($claims as $key => $claim) {
			echo "<tr style=\"$tr_style\">\n";
              echo "<td style=\"$td_style padding-left: 4px;\" align=\"left\">$claim[LocationID]<br />$claim[ClaimID]</td>\n";
              if (strlen($member_billed['BilledName']) > 25) {
                  $member_billed['BilledName'] = substr($member_billed['BilledName'], 0, 25) . "...";
              }
              echo "<td style=\"$td_style\" align=\"left\">$member_billed[BilledName]<br />$claim[ClaimantName]<br />Open/Closed:&nbsp;&nbsp;$claim[ClaimStatus]<br /><br /><br />Description:</td>\n";
              echo "<td style=\"$td_style\" align=\"left\">$claim[ClassCode]<br /></td>\n";
              echo "<td style=\"$td_style\" align=\"center\">$claim[JobTitle]</td>\n";
              echo "<td style=\"$td_style\" align=\"center\">$claim[InjuryDate]<br />$claim[DateClosed]</td>\n";
              echo "<td style=\"$td_style\" align=\"center\">\n";
              echo "<table style=\"$table_style\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">\n";
              echo "<tr style=\"$tr_style\"><td style=\"$td_style\" align=\"left\">Acc: $claim[AccidentDescription]</td></tr>\n";
              echo "<tr style=\"$tr_style\"><td style=\"$td_style\" align=\"left\">Inj: $claim[InjuryDescription]</td></tr>\n";
              echo "<tr style=\"$tr_style\"><td style=\"$td_style\" align=\"left\">Prt: $claim[BodyPart]</td></tr>\n";
              echo "<tr style=\"$tr_style\"><td style=\"$td_style\" align=\"left\">Adj: $claim[Adjuster]</td></tr>\n";
              echo "</table>\n";
              echo "</td>\n";
              echo "<td style=\"$td_style\" align=\"center\">\n";
              echo "<table style=\"$table_style\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">\n";
              echo "<tr style=\"$tr_style\"><td style=\"$td_style\" align=\"right\">Indemnity:</td><td style=\"$td_style\" align=\"right\">\$".number_format($claim['IndemnityReserves'], 2)."</td></tr>\n";
              echo "<tr style=\"$tr_style\"><td style=\"$td_style\" align=\"right\">Medical:</td><td style=\"$td_style\" align=\"right\">\$".number_format($claim['MedicalReserves'], 2)."</td></tr>\n";
              echo "<tr style=\"$tr_style\"><td style=\"$td_style\" align=\"right\">Legal:</td><td style=\"$td_style\" align=\"right\">\$".number_format($claim['LegalReserves'], 2)."</td></tr>\n";
              echo "<tr style=\"$tr_style\"><td style=\"$td_style\" align=\"right\">Other:</td><td style=\"$td_style\" align=\"right\">\$".number_format($claim['OtherReserves'], 2)."</td></tr>\n";
              echo "</table>\n";
              echo "</td>\n";
              echo "<td style=\"$td_style\" align=\"center\">\n";
              echo "<table style=\"$table_style\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">\n";
              echo "<tr style=\"$tr_style\"><td style=\"$td_style\" align=\"right\">\$".number_format($claim['IndemnityPaid'], 2)."</td></tr>\n";
              echo "<tr style=\"$tr_style\"><td style=\"$td_style\" align=\"right\">\$".number_format($claim['MedicalPaid'], 2)."</td></tr>\n";
              echo "<tr style=\"$tr_style\"><td style=\"$td_style\" align=\"right\">\$".number_format($claim['LegalPaid'], 2)."</td></tr>\n";
              echo "<tr style=\"$tr_style\"><td style=\"$td_style\" align=\"right\">\$".number_format($claim['OtherPaid'], 2)."</td></tr>\n";
              echo "</table>\n";
              echo "</td>\n";
              echo "<td style=\"$td_style\" align=\"center\">\n";
              echo "<table style=\"$table_style\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">\n";
              echo "<tr style=\"$tr_style\"><td style=\"$td_style\" align=\"right\">Reserves:</td><td style=\"$td_style\" align=\"right\">\$".number_format($claim['TotalReserves'], 2)."</td></tr>\n";
              echo "<tr style=\"$tr_style\"><td style=\"$td_style\" align=\"right\">Paid:</td><td style=\"$td_style\" align=\"right\">\$".number_format($claim['TotalPaid'], 2)."</td></tr>\n";
              echo "<tr style=\"$tr_style\"><td style=\"$td_style\" align=\"right\">Recovery:</td><td style=\"$td_style\" align=\"right\">\$".number_format($claim['Recovery'], 2)."</td></tr>\n";
              echo "<tr style=\"$tr_style\"><td style=\"$td_style\" align=\"right\">Incurred:</td><td style=\"$td_style\" align=\"right\">\$".number_format($claim['Incurred'], 2)."</td></tr>\n";
              echo "</table>\n";
              echo "</td>\n";
			echo "</tr>\n";
			echo "<tr style=\"$tr_style\">\n";
              echo "<td style=\"$td_style\" >&nbsp;</td>\n";
              echo "<td style=\"$td_style\" align=\"left\" colspan=\"7\">$claim[ClaimDescription]</td>\n";
							//echo "<td align=\"left\" colspan=\"7\">testing</td>\n";
							echo "<td style=\"$td_style\" >&nbsp;</td>\n";
			echo "</tr>\n";
			echo $hr_row;
		}
		// Fund Year totals
		echo "<tr style=\"$tr_style\">\n";
          echo "<td style=\"$td_style\" align=\"left\" colspan=\"9\" class=\"fundYearTotals\">\n";
          echo "<table style=\"$table_style\" cellpadding=\"2\" cellspacing=\"0\"  width=\"100%\">\n";
          echo "<tr style=\"$tr_style\">\n";
          echo "<td style=\"$td_style\" align=\"right\"><strong>Totals for $fundyear[YearCode]<br />Co.# $member_billed[BilledID]</strong></td>\n";
          echo "<td style=\"$td_style\" align=\"left\">\n";
          echo "<table style=\"$table_style\" cellpadding=\"2\" cellspacing=\"0\">\n";
          echo "<tr style=\"$tr_style\">\n";
          echo "<td style=\"$td_style\" align=\"right\">&nbsp;</td>\n";
          echo "<td style=\"$td_style\" align=\"right\"><b>Indemnity</b></td>\n";
          echo "<td style=\"$td_style\" align=\"right\"><b>Medical</b></td>\n";
          echo "<td style=\"$td_style\" align=\"right\"><b>Legal</b></td>\n";
          echo "<td style=\"$td_style\" align=\"right\"><b>Other</b></td>\n";
          echo "<td style=\"$td_style\" align=\"right\"><b>Totals</b></td>\n";
          echo "</tr>\n";
          echo "<tr style=\"$tr_style\">\n";
          echo "<td style=\"$td_style\" align=\"right\">&nbsp;</td>\n";
          echo "<td style=\"$td_style\" align=\"left\" colspan=\"5\"><hr></td>\n";
          echo "</tr>\n";
          echo "<tr style=\"$tr_style\">\n";
          echo "<td style=\"$td_style\" align=\"right\"><b>Reserves:</b></td>\n";
          echo "<td style=\"$td_style\" align=\"right\">\$".number_format($fundyear['FY_IndReserves'], 2)."</td>\n";
          echo "<td style=\"$td_style\" align=\"right\">\$".number_format($fundyear['FY_MedReserves'], 2)."</td>\n";
          echo "<td style=\"$td_style\" align=\"right\">\$".number_format($fundyear['FY_LegReserves'], 2)."</td>\n";
          echo "<td style=\"$td_style\" align=\"right\">\$".number_format($fundyear['FY_OthReserves'], 2)."</td>\n";
          $reserveTotal = $fundyear[FY_IndReserves] + $fundyear[FY_MedReserves] + $fundyear[FY_LegReserves] + $fundyear[FY_OthReserves];
          echo "<td style=\"$td_style\" align=\"right\">\$".number_format($reserveTotal, 2)."</td>\n";
          echo "</tr>\n";
          echo "<tr style=\"$tr_style\">\n";
          echo "<td style=\"$td_style\" align=\"right\"><b>Paid:</b></td>\n";
          echo "<td style=\"$td_style\" align=\"right\">\$".number_format($fundyear[FY_IndPaid], 2)."</td>\n";
          echo "<td style=\"$td_style\" align=\"right\">\$".number_format($fundyear[FY_MedPaid], 2)."</td>\n";
          echo "<td style=\"$td_style\" align=\"right\">\$".number_format($fundyear[FY_LegPaid], 2)."</td>\n";
          echo "<td style=\"$td_style\" align=\"right\">\$".number_format($fundyear[FY_OthPaid], 2)."</td>\n";
          $paidTotal = $fundyear[FY_IndPaid] + $fundyear[FY_MedPaid] + $fundyear[FY_LegPaid] + $fundyear[FY_OthPaid];
          echo "<td style=\"$td_style\" align=\"right\">\$".number_format($paidTotal, 2)."</td>\n";
          echo "</tr>\n";
          echo "<tr style=\"$tr_style\">\n";
          echo "<td style=\"$td_style\" align=\"right\">&nbsp;</td>\n";
          echo "<td style=\"$td_style\" align=\"left\" colspan=\"5\"><hr></td>\n";
          echo "</tr>\n";

          $indemnityTotal = $fundyear[FY_IndReserves] + $fundyear[FY_IndPaid];
          $medicalTotal = $fundyear[FY_MedReserves] + $fundyear[FY_MedPaid];
          $legalTotal = $fundyear[FY_LegReserves] + $fundyear[FY_LegPaid];
          $otherTotal = $fundyear[FY_OthReserves] + $fundyear[FY_OthPaid];
          $allTotal = $indemnityTotal + $medicalTotal + $legalTotal + $otherTotal;
          echo "<tr style=\"$tr_style\">\n";
          echo "<td style=\"$td_style\" align=\"right\"><b>Totals:</b></td>\n";
          echo "<td style=\"$td_style\" align=\"right\">\$".number_format($indemnityTotal, 2)." </td>\n";
          echo "<td style=\"$td_style\" align=\"right\">\$".number_format($medicalTotal, 2)." </td>\n";
          echo "<td style=\"$td_style\" align=\"right\">\$".number_format($legalTotal, 2)." </td>\n";
          echo "<td style=\"$td_style\" align=\"right\">\$".number_format($otherTotal, 2)." </td>\n";
          echo "<td style=\"$td_style\" align=\"right\">\$".number_format($allTotal, 2)."</td>\n";
          echo "</tr>\n";
          echo "</table>\n";
          echo "</td>\n";
          echo "<td style=\"$td_style\" align=\"left\">\n";
          echo "<table style=\"$table_style\" cellpadding=\"2\" cellspacing=\"0\" class=\"withBorder\" width=\"100%\">\n";
          echo "<tr style=\"$tr_style\">\n";
          echo "<td style=\"$td_style\" align=\"center\" class=\"withBorder\"><b>Experience<br />Modifier</b><br /><br />$fundyear[ExMod]</td>\n";
          echo "<td style=\"$td_style\" align=\"right\">\n";
          echo "<table style=\"$table_style\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">\n";
          echo "<tr style=\"$tr_style\"><td style=\"$td_style\"><b>Total&nbsp;Recovery:</b></td></tr>\n";
          echo "<tr style=\"$tr_style\"><td style=\"$td_style\"><b>Total&nbsp;Incurred:</b></td></tr>\n";
          echo "<tr style=\"$tr_style\"><td style=\"$td_style\"><b>Premium:</b></td></tr>\n";
          echo "<tr style=\"$tr_style\"><td style=\"$td_style\"><b>Loss&nbsp;Ratio:</b></td></tr>\n";
          echo "</table>\n";
          echo "</td>\n";
          echo "<td style=\"$td_style\" align=\"right\">\n";
          echo "<table style=\"$table_style\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">\n";
          echo "<tr style=\"$tr_style\"><td style=\"$td_style\">\$".number_format($fundyear[FY_Recovery], 2)."</td></tr>\n";
          echo "<tr style=\"$tr_style\"><td style=\"$td_style\">\$".number_format($fundyear[FY_Incurred], 2)."</td></tr>\n";
          echo "<tr style=\"$tr_style\"><td style=\"$td_style\">\$".number_format($fundyear[FY_Premium], 2)."</td></tr>\n";
          echo "<tr style=\"$tr_style\"><td style=\"$td_style\">$fundyear[FY_LossRatio]&nbsp;%</td></tr>\n";
          echo "</table>\n";
          echo "</td>\n";
          echo "</tr>\n";
          echo "</table>\n";
          echo "</td>\n";
          echo "</tr>\n";
          echo "</table>\n";
          echo "</td>\n";
		echo "</tr>\n";
	}
	// Grand Totals
	echo "<tr style=\"$tr_style\">\n";
				echo "<td style=\"$td_style padding: 0;\" align=\"left\" colspan=\"9\">\n";
				echo "<h3 style=\"$tablelabel_style\">Grand Totals (All Fund Years)</h3>\n";
				echo "</td>\n";
	echo "</tr>\n";
  echo "<tr style=\"$tr_style\">\n";
	echo "<td style=\"$td_style\" align=\"left\" colspan=\"9\" border=\"0\">\n";
      echo "<table style=\"$table_style\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">\n";
      echo "<tr style=\"$tr_style\">\n";
      echo "<td style=\"$td_style\" align=\"left\"><b> Grand Totals for $member_billed[YearCode]<br />Co.# $member_billed[BilledID]</b></td>\n";
      echo "<td style=\"$td_style\" align=\"left\">\n";
      echo "<table style=\"$table_style\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">\n";
      echo "<tr style=\"$tr_style\">\n";
      echo "<td style=\"$td_style\" align=\"right\">&nbsp;</td>\n";
      echo "<td style=\"$td_style\" align=\"right\"><b>Indemnity</b></td>\n";
      echo "<td style=\"$td_style\" align=\"right\"><b>Medical</b></td>\n";
      echo "<td style=\"$td_style\" align=\"right\"><b>Legal</b></td>\n";
      echo "<td style=\"$td_style\" align=\"right\"><b>Other</b></td>\n";
      echo "<td style=\"$td_style\" align=\"right\"><b>Totals</b></td>\n";
      echo "</tr>\n";
      echo "<tr style=\"$tr_style\">\n";
      echo "<td style=\"$td_style\" align=\"right\">&nbsp;</td>\n";
      echo "<td style=\"$td_style\" align=\"left\" colspan=\"5\"><hr></td>\n";
      echo "</tr>\n";
      echo "<tr style=\"$tr_style\">\n";
      echo "<td style=\"$td_style\" align=\"right\"><b>Reserves:</b></td>\n";
      echo "<td style=\"$td_style\" align=\"right\">\$".number_format($member_billed[MB_IndReserves], 2)."</td>\n";
      echo "<td style=\"$td_style\" align=\"right\">\$".number_format($member_billed[MB_MedReserves], 2)."</td>\n";
      echo "<td style=\"$td_style\" align=\"right\">\$".number_format($member_billed[MB_LegReserves], 2)."</td>\n";
      echo "<td style=\"$td_style\" align=\"right\">\$".number_format($member_billed[MB_OthReserves], 2)."</td>\n";
      $reserveTotal = $member_billed[MB_IndReserves] + $member_billed[MB_MedReserves] + $member_billed[MB_LegReserves] + $member_billed[MB_OthReserves];
      echo "<td style=\"$td_style\" align=\"right\">\$".number_format($reserveTotal, 2)."</td>\n";
      echo "</tr>\n";
      echo "<tr style=\"$tr_style\">\n";
      echo "<td style=\"$td_style\" align=\"right\"><b>Paid:</b></td>\n";
      echo "<td style=\"$td_style\" align=\"right\">\$".number_format($member_billed[MB_IndPaid], 2)."</td>\n";
      echo "<td style=\"$td_style\" align=\"right\">\$".number_format($member_billed[MB_MedPaid], 2)."</td>\n";
      echo "<td style=\"$td_style\" align=\"right\">\$".number_format($member_billed[MB_LegPaid], 2)."</td>\n";
      echo "<td style=\"$td_style\" align=\"right\">\$".number_format($member_billed[MB_OthPaid], 2)."</td>\n";
      $paidTotal = $member_billed[MB_IndPaid] + $member_billed[MB_MedPaid] + $member_billed[MB_LegPaid] + $member_billed[MB_OthPaid];
      echo "<td style=\"$td_style\" align=\"right\">\$".number_format($paidTotal, 2)."</td>\n";
      echo "</tr>\n";
      echo "<tr style=\"$tr_style\">\n";
      echo "<td style=\"$td_style\" align=\"right\">&nbsp;</td>\n";
      echo "<td style=\"$td_style\" align=\"left\" colspan=\"5\"><hr></td>\n";
      echo "</tr>\n";

      $indemnityTotal = $member_billed[MB_IndReserves] + $member_billed[MB_IndPaid];
      $medicalTotal = $member_billed[MB_MedReserves] + $member_billed[MB_MedPaid];
      $legalTotal = $member_billed[MB_LegReserves] + $member_billed[MB_LegPaid];
      $otherTotal = $member_billed[MB_OthReserves] + $member_billed[MB_OthPaid];
      $allTotal = $indemnityTotal + $medicalTotal + $legalTotal + $otherTotal;
      echo "<tr style=\"$tr_style\">\n";
      echo "<td style=\"$td_style\" align=\"right\"><b>Totals:</b></td>\n";
      echo "<td style=\"$td_style\" align=\"right\">\$".number_format($indemnityTotal, 2)." </td>\n";
      echo "<td style=\"$td_style\" align=\"right\">\$".number_format($medicalTotal, 2)." </td>\n";
      echo "<td style=\"$td_style\" align=\"right\">\$".number_format($legalTotal, 2)." </td>\n";
      echo "<td style=\"$td_style\" align=\"right\">\$".number_format($otherTotal, 2)." </td>\n";
      echo "<td style=\"$td_style\" align=\"right\">\$".number_format($allTotal, 2)."</td>\n";
      echo "</tr>\n";
      echo "</table>\n";
      echo "</td>\n";
      echo "<td style=\"$td_style\" align=\"left\">\n";
      echo "<table style=\"$table_style\" cellpadding=\"0\" cellspacing=\"0\" class=\"withBorder\">\n";
      echo "<tr style=\"$tr_style\">\n";
      echo "<td style=\"$td_style\" align=\"center\">&nbsp;</td>\n";
      echo "<td style=\"$td_style\" align=\"right\">\n";
      echo "<table style=\"$table_style\" cellpadding=\"0\" cellspacing=\"0\">\n";
      echo "<tr style=\"$tr_style\"><td style=\"$td_style\"><b>Total&nbsp;Recovery:</b></td></tr>\n";
      echo "<tr style=\"$tr_style\"><td style=\"$td_style\"><b>Total&nbsp;Incurred:</b></td></tr>\n";
      echo "<tr style=\"$tr_style\"><td style=\"$td_style\"><b>Premium:</b></td></tr>\n";
      echo "<tr style=\"$tr_style\"><td style=\"$td_style\"><b>Loss&nbsp;Ratio:</b></td></tr>\n";
      echo "</table>\n";
      echo "</td>\n";
      echo "<td style=\"$td_style\" align=\"right\">\n";
      echo "<table style=\"$table_style\" cellpadding=\"0\" cellspacing=\"0\">\n";
      echo "<tr style=\"$tr_style\"><td style=\"$td_style\">\$".number_format($member_billed[MB_Recovery], 2)."</td></tr>\n";
      echo "<tr style=\"$tr_style\"><td style=\"$td_style\">\$".number_format($member_billed[MB_Incurred], 2)."</td></tr>\n";
      echo "<tr style=\"$tr_style\"><td style=\"$td_style\">\$".number_format($member_billed[MB_Premium], 2)."</td></tr>\n";
      echo "<tr style=\"$tr_style\"><td style=\"$td_style\">$member_billed[MB_LossRatio]&nbsp;%</td></tr>\n";
      echo "</table>\n";
      echo "</td>\n";
      echo "</tr>\n";
      echo "</table>\n";
      echo "</td>\n";
      echo "</tr>\n";
      echo "</table>\n";
			echo "</td>\n";
      echo "</tr>\n";
      echo "</table>\n";
      echo "</div>\n"; // end fisif-report

			$html_output = ob_get_clean();
			$report_fn = 'lossrun-report-'.$BilledID;
			$report_title = 'Loss Run Report';
			$report_orientation = 'landscape';

			//$_SESSION['fisiftools'] = '';
			$_SESSION['fisiftools']['report_html'] = $html_output;
			$_SESSION['fisiftools']['report_fn'] = $report_fn;
			$_SESSION['fisiftools']['report_title'] = $report_title;
			$_SESSION['fisiftools']['report_orientation'] = $report_orientation;

			return $html_output;

  }
}
