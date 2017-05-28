<?php

class FT_Agent_Home {

	public function __construct() {

	}

	/**
     * Display the Members - Home
     *
     * @since    0.0.7
     * @access   public
     */

    public function agentSummary() {

        $logout_url = wp_logout_url();

        $current_user = wp_get_current_user();
        $AgentID = str_replace('agent','',$current_user->user_login);
				$xx = new FISIF_Tools_Db();

        // Get the Agency
        $agent = $xx->getUserInfo('agent', $AgentID);

        // Get Associated Members
        $members = $xx->getAssocMembers($AgentID);

        // grab the active member data
        // and set a SESSION var to use to determine active member
        if (isset($_REQUEST['fisifmemberid'])) {
                $_SESSION['fisifmemberid'] = $_REQUEST['fisifmemberid'];
            }
        if (isset($_SESSION['fisifmemberid'])) {
                $MemberID = $_SESSION['fisifmemberid'];
                $member = $xx->getUserInfo('member', $MemberID);
            }

        // account summary
        echo "<div class=\"section-fisifhome-summary-agents\">\n";

        // Display Agent Summary
				$ssm_style = "background-color: #898989; ";
				$ssm_style .= "color: #FFFFFF; font: normal 400 12px/1 'times_new_roman', 'open_sansregular', Arial, sans-serif;";
				$ssm_style .= "padding: 5px; margin: 0;";

        echo "<div class=\"section-fisifhome-left\">\n";
        echo "$BilledID\n";
        echo "<h4 style=\"$ssm_style\">Agent</h4>\n";
        echo "<p><b>$agent[name]</b> ($agent[id])</br>\n";
        echo "$agent[address]<br />\n";
        echo "$agent[city] $agent[zip]<br />\n";
        echo "Phone: $agent[phone]<br />\n";
        echo "Fax: $agent[fax]</p>\n";
				echo "<p><a href=\"/wp-content/uploads/2016/03/FISIF-Coverage-Agreement.pdf\">FISIF Coverage Agreement</a></p>\n";
        echo "</div>\n";

        // Display Active/Selected Member Summary
        echo "<div class=\"section-fisifhome-right\">\n";
        echo "<h4 style=\"$ssm_style\">Selected Member</h4>\n";
        echo "<br/><form>\n";
		echo "<select onChange=\"this.form.submit()\" name=\"fisifmemberid\" id=\"fisifmemberid\">\n";
        // echo "<option value=\"$secureURL/?mod=my-account&action=loginAsMember&member=".$user['member']['id']."\">".$user['member']['name']."</option>\n";
        if (isset($member)) {
            echo "<option value=\"\">$member[name]</option>\n";
            echo "<option value=\"\">----</option>\n";
        } else {
            echo "<option value=\"\">-- Please select a member --</option>\n";
        }
		foreach ($members as $m) {
            if ($m[name]) {
                echo "<option value=\"$m[id]\">$m[name]</option>\n";
            }
        }
		echo "</select>\n";
        echo "</form>\n";
        if (isset($member)) {
            echo "<p>$member[name] ($member[id])<br />\n";
            echo "$member[address] $member[address2]<br />\n";
            echo "$member[city] $member[zip]<br/>\n";
            echo "Phone: $member[phone]<br/><br/>\n";

            echo "<a href=\"/member/member-home/account-receivable-report\">Account Receivable Report</a><br/>\n";
            echo "<a href=\"/member/member-home/premium-computation\">Premium Computation</a><br/>\n";
            echo "<a href=\"/member/member-home/loss-run-report\">Loss Run Report</a><br/>\n";
            echo "<a href=\"/member/member-home/loss-ratio-report\">Loss Ratio Report</a><br/>\n";
						echo "<a href=\"/member/member-home/certificate-of-insurance\">Certificate of Insurance</a><br/>\n";
						if ($member[PRReport] == 'Y'){
							echo "<a href=\"/member/member-home/payroll-report\">Payroll Report Form</a><br/>\n";
						} 

            echo "</p>\n";


        }
        echo "</div>\n";

        // end account summary
        echo "</div>\n";

        echo "<div class=\"section-fisif-report\">\n";
        echo "<h2 align=\"left\">Associated Members</h2>\n";
        echo "<table width=\"100%\" border=\"1\">\n";
        echo "<tr><th>Member ID</th><th>Member Name</th><th>Phone</th></tr>\n";
        foreach ($members as $key => $member) {
            echo "<tr><td align=\"center\">$member[id]</td>";
            echo "<td><a href=\"?fisifmemberid=$member[id]\">$member[name]</a></td><td>$member[phone]</td></tr>\n";
        }
        echo "</table></div>\n";
    }


}
