<?php

class FT_Agent_Premium_Estimator {

	public function __construct() {

	}

    public function premiumEstimator() {

        $this->process();
        $this->displayEstimator();

    }
    public function displayEstimator() {

        $xx = new FISIF_Tools_Db();
        $user = wp_get_current_user();

        $app = $_SESSION['app'];

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

        echo "<div class=\"section-fisif-report\">\n";
        echo "<h3 align=\"right\">$app[agency]</h3><hr/>\n";
        echo "<h1>Agent Premium Estimator</h1>\n";
        echo "<form name=\"premium_estimator\" action=\"\" method=\"POST\">\n";
        // echo "<table>\n";
		// echo "<tr>\n";
        // echo "<td>Member</td><td><input type=\"text\" name=\"app[member]\" id=\"app[member]\" size=\"50\" value=\"$app[member]\" /></td>\n";
		// echo "<tr>\n";
		// echo "<tr>\n";
        // echo "<td>Address</td><td><input type=\"text\" name=\"app[address]\" id=\"app[address]\" size=\"50\" value=\"$app[address]\" /></td>\n";
		// echo "<tr>\n";
		// echo "<tr>\n";
        // echo "<td>City, State and Zip</td><td><input type=\"text\" name=\"app[city_state_zip]\" id=\"app[city_state_zip]\" size=\"60\" value=\"$app[city_state_zip]\" /></td>\n";
		// echo "<tr>\n";
		// echo "<tr>\n";
        // echo "<td>Phone</td><td><input type=\"text\" name=\"app[phone]\" id=\"app[phone]\" size=\"15\" value=\"$app[phone]\" /></td>\n";
		// echo "<tr>\n";
		// echo "<tr>\n";
        // echo "<td>Contact</td><td><input type=\"text\" name=\"app[contact]\" id=\"app[contact]\" size=\"40\" value=\"$app[contact]\" /></td>\n";
		// echo "<tr>\n";
		// echo "<tr>\n";
        // echo "<td>Agency</td><td><input type=\"text\" name=\"app[agency]\" id=\"app[agency]\" size=\"50\" value=\"$app[agency]\" /></td>\n";
		// echo "<tr>\n";
		// echo "<tr>\n";
        // echo "<td>Producer</td><td><input type=\"text\" name=\"app[producer]\" id=\"app[producer]\" size=\"40\" value=\"$app[producer]\" /></td>\n";
		// echo "<tr>\n";
		// echo "<tr>\n";
        // echo "<td>Phone / Fax</td><td><input type=\"text\" name=\"app[producer_phone_fax]\" id=\"app[producer_phone_fax]\" size=\"30\" value=\"$app[producer_phone_fax]\" /></td>\n";
		// echo "<tr>\n";

        // echo "</table>\n";
        // echo "<p><br /></p>\n";
        // echo "<p>\n";
        // echo "<input class=\"formbutton\" type=\"submit\" name=\"submit\" value=\"Calculate\" align=\"center\">  \n";
        // echo "<input class=\"formbutton\" type=\"button\" name=\"reset\" value=\"Reset\" align=\"center\" onClick=\"window.location='?ft-action=reset'\">\n";
        // echo "</p>\n";
        echo "<table cellpadding=\"5\" cellspacing=\"0\" border=\"1\">\n";
		echo "<tr>\n";
        echo "<th>Code Number</th>\n";
        echo "<th>Classification of Operations</th>\n";
        echo "<th>Annual Payroll</th>\n";
        echo "<th>Rate Per \$100</th>\n";
        echo "<th>Estimated Premium</th>\n";
		echo "</tr>\n";

		$rowcnt = 10;
		$loopcnt = 0;

		while ($loopcnt < $rowcnt) {
			echo "<tr>\n";
            echo "<td><input name=\"app[line][$loopcnt][classcode]\" id=\"app[line][$loopcnt][classcode]\" type=\"text\" size=\"10\" value=\"".$app['line'][$loopcnt]['classcode']."\" /></td>\n";
            echo "<td>".$app['line'][$loopcnt]['description']."&nbsp;</td>\n";
            echo "<td>\$<input name=\"app[line][$loopcnt][annual_payroll]\" id=\"app[line][$loopcnt][annual_payroll]\" type=\"text\" size=\"10\" value=\"".number_format($app['line'][$loopcnt]['annual_payroll'])."\" /></td>\n";
            echo "<td>\$".$app['line'][$loopcnt]['rate_per_100']."</td>\n";
            echo "<td>\$".number_format($app['line'][$loopcnt]['estimated_premium'])."</td>\n";
			echo "</tr>\n";
			$loopcnt++;
		}
		echo "<tr>\n";
        echo "<td colspan=\"2\"><b>Sub Totals:</b></td>\n";
        echo "<td colspan=\"2\"><b>\$".number_format($app['sub_annual_payroll'])."</b></td>\n";
        echo "<td><b>\$".number_format($app['sub_estimated_premium'])."</b></td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
        echo "<td colspan=\"5\">&nbsp;</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
        echo "<td colspan=\"2\">Experience Modification:</td>\n";
        echo "<td colspan=\"2\"><input name=\"app[experience_modification]\" id=\"app[experience_modification]\" type=\"text\" size=\"3\" value=\"".$app['experience_modification']."\"/></td>\n";
        echo "<td>\$".number_format($app['experience_modification_amount'])."</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
        echo "<td>Deductible</td>\n";
        echo "<td>\n";
        echo "<select id=\"app[deductible_amount]\" name=\"app[deductible_amount]\">\n";
        if (!empty($app['deductible_amount'])) {
            echo "<option value=\"$app[deductible_amount]\">\$".number_format($app['deductible_amount'])."</option>\n";
        }
        echo "<option value=\"\">------</option>\n";
        echo "<option value=\"500\">\$500</option>\n";
        echo "<option value=\"1000\">\$1,000</option>\n";
        echo "<option value=\"2500\">\$2,500</option>\n";
        echo "<option value=\"5000\">\$5,000</option>\n";
        echo "<option value=\"10000\">\$10,000</option>\n";
        echo "</select>\n";
        echo "</td>\n";
        echo "<td>$app[deductible_percentage]%</td>\n";
        echo "<td>-\$".$app['deductible_calc']."</td>\n";
        echo "<td>\$".number_format($app['deductible_amount_total'])."</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
        echo "<td colspan=\"2\">Volume Discount</td>\n";
        echo "<td>$app[volume_discount_percentage]%</td>\n";
        echo "<td>-\$".$app['volume_discount_calc']."</td>\n";
        echo "<td>\$".number_format($app['volume_discount_amount_total'])."</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
        echo "<td colspan=\"5\">&nbsp;</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
        echo "<td colspan=\"4\"><b><i>Annualized</i></b></td>\n";
        echo "<td><b>\$".number_format($app['annualized_premium'])."</b></td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
        echo "<td colspan=\"5\">\n";
        echo "<p>\n";
        echo "<input class=\"formbutton\" type=\"submit\" name=\"submit\" value=\"Calculate\" align=\"center\">  \n";
        echo "<input class=\"formbutton\" type=\"button\" name=\"reset\" value=\"Reset\" align=\"center\" onClick=\"window.location='?ft-action=reset'\">\n";
        echo "</p>\n";
        echo "<input type=\"hidden\" name=\"ft-action\" value=\"calculate\" />\n";
        echo "</td>\n";
		echo "</tr>\n";

        echo "</table></form>\n";
        echo "</div>\n";
				$this->disclaimer();
    }
		function disclaimer() {
			$disclaimer = "<p><strong>PLEASE NOTE:</strong>  The FISIF.com Premium Estimator is intended to provide you with an annual premium estimate based on the information you ";
			$disclaimer .= "provided.  This is not a quote or an offer of coverage from FISIF.   To obtain a quote, please submit an ACORD Application and 3-5 ";
			$disclaimer .= "years loss history to FISIF here.</p><p><a href='/agent/contact-underwriting-staff-submit-application/'><strong>Submit a Quote Application</srong></a></p>";

			echo $disclaimer;
		}
    function process() {

        $xx = new FISIF_Tools_Db();

        $action = $_REQUEST['ft-action'];

        switch($action) {

        case "reset":
            unset($_SESSION['app']);
            break;

        case "calculate":

            if (isset($_POST['app'])) {
                $_SESSION['app'] = $_POST['app'];
                $app = $_SESSION['app'];  // shorthand

                // SET DEFAULT VALUES
                if ($app['experience_modification'] == "") { $app['experience_modification'] = "1.0"; }

                //$xx = new vsDb();
                foreach ($app['line'] as $key => $data) {

                    // get the associated rate data for each line
                    if (!empty($data['classcode'])) {
                        $tablename = "rates";
                        $params = "WHERE `classcode`='".$data['classcode']."'";
                        $result = $xx->GetRecords("*", $tablename, $params);
                        $rate = $result[0];

                        if ($rate) {
                            $app['line'][$key]['description'] = $rate['description'];
                            $app['line'][$key]['rate'] = $rate['rate'];
                            $app['line'][$key]['minimum_premium'] = $rate['minimum_premium'];
                            $app['line'][$key]['annual_payroll'] = str_replace(",", "", $app['line'][$key]['annual_payroll']);

                            $app['line'][$key]['rate_per_100'] = $rate['rate'];
                            $app['line'][$key]['estimated_premium'] = $app['line'][$key]['annual_payroll'] * ($rate['rate'] / 100);
                            $rate = $rate['rate'];
                            $ap = $app['line'][$key]['annual_payroll'];
                            $premium = $app['line'][$key]['estimated_premium'];
                            // echo "$key::Estimated Premium: $ap * ($rate / 100) = $premium<br />\n";

                            $app['sub_annual_payroll'] = $app['sub_annual_payroll'] + $app['line'][$key]['annual_payroll'];
                            $app['sub_estimated_premium'] = $app['sub_estimated_premium'] + $app['line'][$key]['estimated_premium'];

                            // $app['annualized_premium'] = $app['annualized_premium']   $app['line'][$key]['manual_premium'];
                        } else {
                            $app['line'][$key]['annual_payroll'] = str_replace(",", "", $app['line'][$key]['annual_payroll']);
                            $app['line'][$key]['description'] = "--code not found - contact FISIF staff--";
                        }
                    }
                }
                $app['experience_modification_amount'] = $app['experience_modification'] * $app['sub_estimated_premium'];
                switch ($app['deductible_amount']) {
                case "500":
                    $app['deductible_percentage'] = "5.75";
                    break;
                case "1000":
                    $app['deductible_percentage'] = "8.5";
                    break;
                case "1500":
                    $app['deductible_percentage'] = "10";
                    break;
                case "5000":
                    $app['deductible_percentage'] = "15";
                    break;
                case "10000":
                    $app['deductible_percentage'] = "20";
                    break;
                default:
                    $app['deductible_percentage'] = "";
                }
                $app['deductible_calc'] = round($app['experience_modification_amount'] * ($app['deductible_percentage'] / 100));
                $app['deductible_amount_total'] = $app['experience_modification_amount'] - $app['deductible_calc'];

                // Get the Volume Discount based on the Deductible Amount Total

                $tablename = "pe_discounts";
                $params = "WHERE '".$app['deductible_amount_total']."' BETWEEN base AND top ";
                $result = $xx->GetRecords("discount", $tablename, $params);
                $app['volume_discount_percentage'] = $result[0]['discount'];

                if (!empty($app['volume_discount_percentage']) && $app['volume_discount_percentage'] !== "0") {
                    $app['volume_discount_calc'] = round($app['deductible_amount_total'] * ($app['volume_discount_percentage'] / 100));
                } else {
                    $app['volume_discount_calc'] = "0.00";
                }

                $app['volume_discount_amount_total'] = $app['deductible_amount_total'] - $app['volume_discount_calc'];

                $app['annualized_premium'] = ceil($app['volume_discount_amount_total']);

                $_SESSION['app'] = $app;

            }
            break;

        default:
            break;
        }
    }
}
