<?php

class FT_Agent_Commission_Report extends FISIF_Tools_Public {

	protected $_user = false;
	protected $_fundyear = false;
	protected $_classcodes = false;
	protected $_discount = false;
	protected $_db = false;

	public function __construct() {

	}

	/**
     * Display the Members - Account Receivable Report
     *
     * @since    0.0.7
     * @access   public
     */
    public function agentCommissionReport() {

				$this->_db = new FISIF_Tools_Db(); // a single db object for all calls
				$this->_user = wp_get_current_user();

        // Get the Agent Commission Data
				$agent_commissions = $this->getAgentCommissions();

        // Get the Agent Commission Member Data
				$agent_commissions_members
					= $this->getAgentCommissionsMembers($agent_commissions['AgencyName']);

        $the_date = date('F d, Y');

				// place output in buffer
				ob_start();

        echo "<div class=\"section-fisif-report\">\n";

        echo "<h4 align=\"center\">Food Industry Insurance Fund of New Mexico</h4>\n";
        echo "<p align=\"center\">P.O. Box 14710\n";
        echo "<br/>Albuquerque, NM 87191-4710</p>\n";

        echo "<h3 class=\"report-title\">Agency Commissions</h3>\n";
        echo "<p class=\"report-date\">$the_date</p>\n";
        echo "<hr/>\n";
        echo "<table class=\"report-header\"><tr>\n";
				echo "<td class=\"report-header-left\">\n";
	            echo "$agent_commissions[AgencyName]<br/>\n";
	            echo "$agent_commissions[AgencyAddr1]<br/>\n";
	            echo "$agent_commissions[AgencyAddr2]<br/>\n";
	            echo "$agent_commissions[AgencyCity], $agent[AgencyState] $agent[AgencyZip]<br/>\n";
	            echo "$agent_commissions[AgencyPhone] - Fax: $agent[AgencyFax]</td>\n";


        echo "<td class=\"report-header-right\">\n";
        echo "<strong>Coverage Code:</strong> $agent_commissions[YearCode]<br/>\n";
        echo "<strong>Period Beginning:</strong> $agent_commissions[PeriodBeginDate]<br/>\n";
        echo "<strong>Period Ending:</strong> $agent_commissions[PeriodEndDate]<br/>\n";
        echo "</td>\n";
        echo "</tr></table>\n";

        echo "<div class=\"agent_commissions_members\"><table>\n";

				// create total defaults
				$total_premium = 0;
				$total_earned = 0;
				$total_premium_paid = 0;
				$total_paid_premium_earned = 0;
				$total_com_earned = 0;
				$total_com_paid = 0;
				$total_balance_due = 0;
				$total_amount_paid = 0;

				// generate output
				$th_row =  "<tr>\n";
		        $th_row .= "<th align=\"left\">Audit</th>\n";
		        $th_row .= "<th align=\"left\">ID</th>\n";
		        $th_row .= "<th align=\"left\">Name</th>\n";
		        $th_row .= "<th>Begin</th>\n";
		        $th_row .= "<th>End</th>\n";
		        $th_row .= "<th align=\"right\">Premium</th>\n";
		        $th_row .= "<th align=\"right\">Earned</th>\n";
		        $th_row .= "<th align=\"right\">Premium Paid</th>\n";
		        $th_row .= "<th align=\"right\">Paid Prem Earned</th>\n";
		        $th_row .= "<th align=\"right\">Rate</th>\n";
		        $th_row .= "<th align=\"right\">Com Earned</th>\n";
		        $th_row .= "<th align=\"right\">Com Paid</th>\n";
		        $th_row .= "<th align=\"right\">Balance Due</th>\n";
		        $th_row .= "<th align=\"right\">Amount To Be Paid</th>\n";
				$th_row .= "</tr>\n";

				$hr_row = "<tr>\n";
		        $hr_row .= "<td colspan=\"14\"><hr></td>\n";
				$hr_row .= "</tr>\n";

				echo $th_row;

				foreach ($agent_commissions_members as $c){

					// increment Totals
					$total_premium = $total_premium + $c['Premium'];
					$total_earned = $total_earned + $c['Earned'];
					$total_premium_paid = $total_premium_paid + $c['PremiumPaid'];
					$total_paid_premium_earned = $total_paid_premium_earned + $c['PaidPremiumEarned'];
					$total_com_earned = $total_com_earned + $c['ComEarned'];
					$total_com_paid = $total_com_paid + $c['ComPaid'];
					$total_balance_due = $total_balance_due + $c['BalanceDue'];
					$total_amount_paid = $total_amount_paid + $c['AmountPaid'];

					// generate output
					$td_row = "<tr>\n";
			        $td_row .= "<td align=\"left\">".$c['AuditDate']."</td>\n";
			        	$td_row .= "<td align=\"left\">".$c['CorpBilledID']."</td>\n";
			        $td_row .= "<td align=\"left\">".$c['BilledName']."</td>\n";
			        $td_row .= "<td>".$c['PolicyBeginDate']."</td>\n";
			        $td_row .= "<td>".$c['PolicyEndDate']."</td>\n";
			        $td_row .= "<td align=\"right\">".number_format($c['Premium'],2)."</td>\n";
			        $td_row .= "<td align=\"right\">".number_format($c['Earned'],2)."</td>\n";
			        $td_row .= "<td align=\"right\">".number_format($c['PremiumPaid'],2)."</td>\n";
			        $td_row .= "<td align=\"right\">".number_format($c['PaidPremiumEarned'],2)."</td>\n";
			        $td_row .= "<td align=\"right\">".$c['Rate']."</td>\n";
			        $td_row .= "<td align=\"right\">".number_format($c['ComEarned'],2)."</td>\n";
			        $td_row .= "<td align=\"right\">".number_format($c['ComPaid'],2)."</td>\n";
			        $td_row .= "<td align=\"right\">".number_format($c['BalanceDue'],2)."</td>\n";
			        $td_row .= "<td align=\"right\">".number_format($c['AmountPaid'],2)."</td>\n";
					$td_row .= "</tr>\n";
					echo $td_row;
				}

        echo $hr_row;

				// generate output for total row
				$td_row = "<tr>\n";
						$td_row .= "<td align=\"right\" colspan=\"5\"><strong>Totals for $agent_commissions[AgencyName]:</strong></td>\n";;
						$td_row .= "<td align=\"right\">".number_format($total_premium,2)."</td>\n";
						$td_row .= "<td align=\"right\">".number_format($total_earned,2)."</td>\n";
						$td_row .= "<td align=\"right\">".number_format($total_premium_paid,2)."</td>\n";
						$td_row .= "<td align=\"right\">".number_format($total_paid_premium_earned,2)."</td>\n";
						$td_row .= "<td align=\"right\"></td>\n";
						$td_row .= "<td align=\"right\">".number_format($total_com_earned,2)."</td>\n";
						$td_row .= "<td align=\"right\">".number_format($total_com_paid,2)."</td>\n";
						$td_row .= "<td align=\"right\">".number_format($total_balance_due,2)."</td>\n";
						$td_row .= "<td align=\"right\">".number_format($total_amount_paid,2)."</td>\n";
				$td_row .= "</tr>\n";
				echo $td_row;
        // echo "<tr>\n";
        // echo "<td align=\"left\" colspan=\"4\"><b> Grand Totals  ($member_billed[MemberTrans] total transactions)</b></td>\n";
        // echo "<td align=\"right\"><b>\$".number_format($member_billed[MemberCharges], 2)."</b></td>\n";
        // echo "<td align=\"right\"><b>\$".number_format($member_billed[MemberPayments], 2)."</b></td>\n";
        // echo "<td align=\"right\"><b>\$".number_format($member_billed[MemberOther], 2)."</b></td>\n";
        // echo "<td align=\"right\"><b>\$".number_format($member_billed[MemberBalance], 2)."</b></td>\n";
        // echo "</tr>\n";
        echo "</table></div>\n";

        echo "</div>\n"; // End of section-fisif-report

				$html_output = ob_get_clean();

				// grab the associated Agent data
				$user = $this->_user;
				$AgentID = str_replace('agent','',$user->user_login);
				$report_fn = 'agent_commissions-'.$AgentID;
				$report_title = 'Agent Commissions Report';
				$report_orientation = 'landscape';

				//$_SESSION['fisiftools'] = '';
				$_SESSION['fisiftools']['report_html'] = $html_output;
				$_SESSION['fisiftools']['report_fn'] = $report_fn;
				$_SESSION['fisiftools']['report_title'] = $report_title;
				$_SESSION['fisiftools']['report_orientation'] = $report_orientation;

				return $html_output;
    }
		public function getAgentCommissions(){

			$user = $this->_user;

			// grab the associated Agent data
			$AgentID = str_replace('agent','',$user->user_login);
			$agent = $this->_db->getUserInfo('agent', $AgentID);
			$AgencyName = $agent['name'];

			$tablename = "agent_commissions";
			$params = "WHERE `AgencyName`='$AgencyName'";
			$agent_commissions = $this->_db->GetRecords("*", $tablename, $params);
			return $agent_commissions[0];

		}
		public function getAgentCommissionsMembers($AgencyName){

			$user = $this->_user;
			$BilledID = $user->user_login;

			$fundyear = $this->_fundyear;
			$yearcode = $fundyear['YearCode'];
			//$fundyear = $result[0]['YearCode'];

			$tablename = "agent_commissions_members";
			$params = "WHERE `AgencyName`='$AgencyName' ORDER BY `AgencyName` ASC";
			$agent_commissions_members = $this->_db->GetRecords("*", $tablename, $params);
			return $agent_commissions_members;

		}
}
