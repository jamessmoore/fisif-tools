<?php

class FT_Payroll_Report {

	protected $_user = false;
	protected $_fundyear = false;
	protected $_classcodes = false;
	protected $_discount = false;
	protected $_db = false;
	public function __construct() {

	}

    public function payrollReport() {
			$this->_db = new FISIF_Tools_Db(); // a single db object for all calls
			$this->_user = wp_get_current_user();
			$this->_fundyear = $this->getFundYear();
			$this->_classcodes = $this->getClassCodes();
			$this->_discount = $this->getDiscount();

      $this->process();
      return $this->displayForm();

    }

		public function payrollReportLink() {

			$user = wp_get_current_user();
			if (isset ($_SESSION['fisifmemberid']) && (in_array( 'agent', (array) $user->roles )) ) {
					$BilledID = $_SESSION['fisifmemberid'];
			} else {
					$BilledID = $user->user_login;
			}

			$xx = new FISIF_Tools_Db();

			// grab the associated member data
			$member = $xx->getUserInfo('member', $BilledID);

			if ($member[PRReport] == 'Y'){
				$res = "<li><a href=\"/member/member-home/payroll-report\">Payroll Report Form</a></li>\n";
			} else {
				$res = "\n";
			}

			return $res;
    }

    public function displayForm() {

        $xx = new FISIF_Tools_Db();
        $user = wp_get_current_user();

        $app = $_SESSION['app'];
				$ftaction = $_SESSION['app']['ft-action'];

				// values for testing
				$T_rate = '1.58';

        //if (!isset($_SESSION['app']['agency']) || empty($_SESSION['app']['agency'])) { $_SESSION['app']['agency'] = $_SESSION['user']['name']; }
        if (isset ($_SESSION['fisifmemberid']) && (in_array( 'agent', (array) $user->roles )) ) {
            $MemberID = $_SESSION['fisifmemberid'];
            $member = $xx->getUserInfo('member', $MemberID);

            // var_dump($member);
            $app["member"] = $member['name'];
            $app["address"] = $member['address'].' '.$member['address2'];
            $app["city_state_zip"] = $member['city'].', '.$member['state'].' '.$member['zip'];
            $app["phone"] = $member['phone'];

            // var_dump($user);
            $app["agency"] = $user->display_name;
        }

				// Define inline styles for report elements
				if ($ftaction != 'Print'){
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
					$total_style = "font-size: 20px; font-weight: bold;";
				} else {
					// styles tweaked for printing
					$div_style = "background-color: #FFFFFF; padding: 0; font: normal 400 10px/1 'open_sansregular', Arial, sans-serif;";
					$table_style = "padding: 0; margin: 0; width: 100%;";
					$tablelabel_style = "background-color: #FFFFFF; border-bottom: 0px solid #000000;";
					$tablelabel_style .= "color: #000000; font: normal 400 16px/1 'times_new_roman', 'open_sansregular', Arial, sans-serif;";
					$tablelabel_style .= "padding: 5px; margin: 0; margin-top: 20px;";
					$tr_style = "padding: 2px 0;";
					$td_style = "padding: 2px; font: normal 400 9px/1 'times_new_roman', 'open_sansregular', Arial, sans-serif;";
					$th_style = "background-color: #FFFFFF; border-bottom: 0px solid #000000;";
					$th_style .= "color: #000000; font: normal 400 9px/1 'times_new_roman', 'open_sansregular', Arial, sans-serif;";
					$th_style .= " padding: 5px 0; text-transform: uppercase;";
					$p_style = "padding: 2px; font: normal 400 8px/1 'times_new_roman', 'open_sansregular', Arial, sans-serif;";
					$total_style = "font-size: 16px; font-weight: bold;";

				}

				// horizontal table row seperator
				$hr_row = "<tr style=\"$tr_style\">\n";
						$hr_row .= "<td colspan=\"6\" align=\"center\"><hr style=\"width:100%;\"></td>\n";
				$hr_row .= "</tr>\n";

				// empty table row seperator
				$empty_row = "<tr style=\"$tr_style\">\n";
						$empty_row .= "<td colspan=\"5\" align=\"center\">&nbsp;</td>\n";
				$empty_row .= "</tr>\n";

				// place output in buffer
				ob_start();

        echo "<div class=\"section-fisif-report\">\n";
        echo "<h3>Payroll Reporting Form for ".$user->{'display_name'}."</h3><hr/>\n";
				echo "<div align=\"right\"><table cellpadding=\"5\" cellspacing=\"0\" width=\"400px\" style=\"$table_style\">\n";
				echo "<tr style=\"$tr_style\">\n";
		        echo "<td align=\"right\">Remit by:</td><td align=\"left\">20th of the following month</td>\n";
				echo "</tr>\n";
				echo "<tr style=\"$tr_style\">\n";
		        echo "<td align=\"right\">To:</td><td align=\"left\">";
						echo "<strong>Food Industry Self Insurance Fund of NM</strong><br/>";
						echo "P.O. Box 14710<br/>Albuquerque, NM 87191-4710";
						echo "</td>\n";
				echo "</tr>\n";
				echo "</table></div>\n";

				if ($ftaction != 'Print'){
  				echo "<form name=\"payroll_report\" id=\"payroll_report\" action=\"\" method=\"POST\">\n";
					echo "<input type=\"hidden\" id=\"ft-action\" name=\"ft-action\" value=\"\" />\n";
					echo "<input type=\"hidden\" id=\"export\" name=\"export\" value=\"\" />\n";
				}

		    echo "<table cellpadding=\"5\" cellspacing=\"0\" border=\"1\">\n";
				echo "<tr style=\"$tr_style\">\n";
		        echo "<th style=\"$th_style\">State</th>\n";
		        echo "<th style=\"$th_style\">Class</th>\n";
		        echo "<th style=\"$th_style\">Class Description</th>\n";
		        echo "<th style=\"$th_style\">Payroll</th>\n";
						echo "<th style=\"$th_style\">Rate</th>\n";
		        echo "<th style=\"$th_style\">Premium (Payroll/100)*Rate</th>\n";
				echo "</tr>\n";

				$rowcnt = 8;
				$loopcnt = 0;

				while ($loopcnt < $rowcnt) {

					$classcode = isset($app['line'][$loopcnt]['classcode']) ? $app['line'][$loopcnt]['classcode'] : '';
					$description = isset($app['line'][$loopcnt]['description']) ? $app['line'][$loopcnt]['description'] : '';
					$payroll = isset($app['line'][$loopcnt]['payroll']) ? $app['line'][$loopcnt]['payroll'] : '0';
					$rate = isset($app['line'][$loopcnt]['rate']) ? $app['line'][$loopcnt]['rate'] : '0';
					$premium_rate = isset($app['line'][$loopcnt]['premium_rate']) ? $app['line'][$loopcnt]['premium_rate'] : '0';

					echo "<tr style=\"$tr_style\">\n";
					echo "<td>NM&nbsp;</td>\n";
					echo "<td>".$classcode."</td>\n";
					if ($ftaction == 'Print'){
						echo "<td>".$description."</td>\n";
						echo "<td>\$".number_format($payroll)."</td>\n";
					} else {
								echo "<td><select class=\"auto-submit-item\" name=\"app[line][$loopcnt][classcode]\" id=\"app[line][$loopcnt][classcode]\">\n";
				        if ($classcode && $description) {
				            echo "<option value=\"".$classcode."\">".$description."</option>\n";
				            echo "<option value=\"\">----</option>\n";
				        } else {
				            echo "<option value=\"\">----</option>\n";
				        }
								foreach ($this->_classcodes as $c) {
						        echo "<option value=\"$c[ClassCode]\">$c[Description]</option>\n";
						    }
						echo "</select></td>\n";
						echo "<td>\$<input class=\"auto-submit-item\" name=\"app[line][$loopcnt][payroll]\" id=\"app[line][$loopcnt][payroll]\" type=\"text\" size=\"10\" value=\"".number_format($payroll)."\" /></td>\n";
					}
					echo "<td>".$rate."</td>\n";
					echo "<td align=\"right\">\$".number_format($premium_rate,2)."</td>\n";
					echo "</tr>\n";
					$loopcnt++;
				}
				if ($ftaction != 'Print'){ echo $empty_row; echo $hr_row; }
				echo "<tr style=\"$tr_style\">\n";
		        echo "<td colspan=\"3\" align=\"right\"><strong>Total Payroll:</strong></td>\n";
						echo "<td colspan=\"3\" align=\"left\">\$".number_format($app['total_payroll'],2)."</td>\n";
				echo "</tr>\n";
				echo "<tr style=\"$tr_style\">\n";
		        echo "<td colspan=\"5\" align=\"right\"><strong>TOTAL PREMIUM (Add Premium Column):</strong></td>\n";
						echo "<td colspan=\"1\" align=\"right\">\$".number_format($app['total_premium'],2)."</td>\n";
				echo "</tr>\n";
				echo "<tr style=\"$tr_style\">\n";
		        echo "<td colspan=\"4\" align=\"right\"><strong>MODIFIED PREMIUM (Total Premium * Modifier):</strong></td>\n";
						echo "<td colspan=\"1\" align=\"right\" class=\"boxed rate\">".$this->_fundyear['ExMod']."</td>\n";
						echo "<td colspan=\"1\" align=\"right\">\$".number_format($app['modified_premium'],2)."</td>\n";
				echo "</tr>\n";
				// Merit Credit
				echo "<tr style=\"$tr_style\">\n";
		        echo "<td colspan=\"4\" align=\"right\"><strong>".$this->_discount['DiscDescription']." (Credit Rate * Above Premium):</strong></td>\n";
						echo "<td colspan=\"1\" align=\"left\" class=\"boxed rate\">".$this->_discount['DiscRate']."</td>\n";
						echo "<td colspan=\"1\" align=\"right\">\$".number_format($app['merit_credit'],2)."</td>\n";
				echo "</tr>\n";
				echo "<tr style=\"$tr_style\">\n";
		        echo "<td colspan=\"4\" align=\"right\"><strong>STOCK VOLUME DISCOUNT (Disc. Rate * Premium After Above Credit has been Deducted):</strong></td>\n";
						echo "<td colspan=\"1\" align=\"right\" class=\"boxed rate\">".$this->_fundyear['VolumeDiscount']."</td>\n";
						echo "<td colspan=\"1\" align=\"right\">\$".number_format($app['stock_volume_discount'],2)."</td>\n";
				echo "</tr>\n";
				echo "<tr style=\"$tr_style\">\n";
		        echo "<td colspan=\"5\" align=\"right\"><strong>AMOUNT DUE (Modified Prem <Less> Discounts):</strong></td>\n";
						echo "<td colspan=\"1\" align=\"right\" style=\"$total_style\">\$".number_format($app['amount_due'],2)."</td>\n";
				echo "</tr>\n";
				echo "<tr style=\"$tr_style\">\n";
		        echo "<td colspan=\"6\" align=\"left\">";
							echo "<strong>Notes:</strong><br/>";
							echo "Please watch CLASS CODES carefully.<br/>";
							echo "1/3 of OVERTIME may be excluded.<br/>";
							echo "All TIPS may be excluded.<br/>";
							echo "PAYROLL AUDITS may vary if these items are not followed closely!<br/>";
						echo "</td>\n";
				echo "</tr>\n";
				if ($ftaction != 'Print'){
						echo $empty_row;
		        echo "<tr style=\"$tr_style\"><td colspan=\"3\" align=\"right\">\n";
							echo " Update the form and calculated totals.";
		        	echo "</td><td colspan=\"3\" align=\"left\">\n";
							echo "<input class=\"formbutton\" type=\"button\" name=\"ft-action\" id=\"ft-action\" value=\"Calculate\" onClick=\"document.getElementById('ft-action').value = 'Calculate';document.getElementById('export').value = '';submit();\">\n";
						echo "</td></tr><tr style=\"$tr_style\">\n";
							echo "<td colspan=\"3\" align=\"right\">\n";
							echo " Reset the form and all calculations.";
							echo "</td><td colspan=\"3\" align=\"left\">\n";
							echo "<input class=\"formbutton\" type=\"button\" name=\"ft-action\" id=\"ft-action\" value=\"Reset\" onClick=\"document.getElementById('ft-action').value = 'Reset';document.getElementById('export').value = '';submit();\">\n";
						echo "</td></tr>\n";
						echo "<tr style=\"$tr_style\"><td colspan=\"3\" align=\"right\">\n";
							echo " Print this Report.";
		        	echo "</td><td colspan=\"3\" align=\"left\">\n";
							echo "<input class=\"formbutton\" type=\"button\" name=\"ft-action\" id=\"ft-action\" value=\"Print\" onClick=\"document.getElementById('ft-action').value = 'Print';document.getElementById('export').value = 'print';submit();\">\n";
						echo "</td></tr><tr>\n";
			  }
				echo "</table>\n";
				if ($ftaction != 'Print') {
						echo "</form>\n";
				}
        echo "</div>\n";

				$html_output = ob_get_clean();
			  //var_dump($app);
				$report_fn = 'payrollreport-test';
				$report_title = 'Payroll Report';
				$report_orientation = 'portrait';

				$_SESSION['fisiftools'] = '';
				$_SESSION['fisiftools']['report_html'] = $html_output;
				$_SESSION['fisiftools']['report_fn'] = $report_fn;
				$_SESSION['fisiftools']['report_title'] = $report_title;
				$_SESSION['fisiftools']['report_orientation'] = $report_orientation;

				return $html_output;
    }
    public function process() {

        $action = isset($_REQUEST['ft-action']) ? $_REQUEST['ft-action'] : '';
				$_SESSION['app']['ft-action'] = $action;

        switch($action) {

        case "Reset":
            unset($_SESSION['app']);
            break;

			  case "Print":
						// hack for implementing wp print function
						// missing break is purposeful
						$_GET['export'] = 'print';

        case "Calculate":
				default:
					$app = $_SESSION['app'];

          if (isset($_POST['app'])) {
							// set the passed values from POST
							$postapp = $_POST['app'];
							$line = $postapp['line']; // array()
							$total_payroll = 0;
							$total_premium = 0;

            	foreach ($line as $key => $data) {

                    // get the associated rate data for each line
                    if (!empty($data['classcode'])) {

											// payroll from post data
											if ($postapp['line'][$key]['payroll']){
												$app['line'][$key]['payroll'] = $postapp['line'][$key]['payroll'];
											}
											$app['line'][$key]['payroll'] = str_replace(",", "", $app['line'][$key]['payroll']);

											// get the rate from _classcodes
											foreach ($this->_classcodes as $cc){
													if ($cc['ClassCode'] == $data['classcode']){
															$app['line'][$key]['classcode'] = $cc['ClassCode'];
	                            $app['line'][$key]['description'] = $cc['Description'];
	                            $app['line'][$key]['rate'] = $cc['Rate'];

															// calculate premium rate
															$app['line'][$key]['premium_rate'] = ($app['line'][$key]['payroll'] / 100) * $cc['Rate'];

															// caclulate total_payroll
															$total_payroll = $total_payroll + $app['line'][$key]['payroll'];

															// calculate total_premium
															$premium = $app['line'][$key]['premium_rate'];
															$total_premium = $total_premium + $premium;
													}
											}
                    }
                }

								//
								$app['total_payroll'] = $total_payroll;
								$app['total_premium'] = $total_premium;

								// calculate the modified premium
								$ExMod = $this->_fundyear['ExMod'];
								$app['modified_premium'] = $total_premium * $ExMod;

								// calculate merit credit
								$app['merit_credit'] = $app['modified_premium'] * ($this->_discount['DiscRate'] / 100);

								// calculate deductible
								$app['deductible'] = 0;

								// calculate stock volume discount
								$app['stock_volume_discount'] = ($app['modified_premium'] - $app['merit_credit']) * ($this->_fundyear['VolumeDiscount'] / 100);

								//calculate amount due
								$app['amount_due'] = $app['modified_premium'] - ($app['merit_credit'] + $app['stock_volume_discount']);

            }
						$_SESSION['app'] = $app;
            break;
        }
    }
		public function getClassCodes(){

			$user = $this->_user;
			$BilledID = $user->user_login;

			$fundyear = $this->_fundyear;
			$yearcode = $fundyear['YearCode'];
			//$fundyear = $result[0]['YearCode'];

			$tablename = "members_prr_classcodes";
			$params = "WHERE `CorpBilledID`='$BilledID' AND `YearCode` = '$yearcode' ORDER BY `ClassCode` ASC";
			$classcodes = $this->_db->GetRecords("YearCode, ClassCode, Description, Rate", $tablename, $params);
			return $classcodes;

		}
		public function getFundYear(){

			$user = $this->_user;
			$BilledID = $user->user_login;

			// get the latest (current) YearCode
			$tablename = "members_prr_fundyear";
			$params = "WHERE `CorpBilledID`='$BilledID' ORDER BY `YearCode` DESC LIMIT 1";
			$result = $this->_db->GetRecords("*", $tablename, $params);

			return $result[0];
		}
		public function getDiscount(){

			$user = $this->_user;
			$BilledID = $user->user_login;

			// get the latest (current) YearCode
			$tablename = "members_prr_discount";
			$params = "WHERE `CorpBilledID`='$BilledID' ORDER BY `YearCode` DESC LIMIT 1";
			$result = $this->_db->GetRecords("*", $tablename, $params);

			return $result[0];
		}
}
