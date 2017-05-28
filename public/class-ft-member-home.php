<?php

class FT_Member_Home {


	public function __construct() {

	}

	/**
     * Display the Members - Home
     *
     * @since    0.0.7
     * @access   public
     */

    public function memberSummary() {

        $logout_url = wp_logout_url();

				$user = wp_get_current_user();
	      if (isset ($_SESSION['fisifmemberid']) && (in_array( 'agent', (array) $user->roles )) ) {
	          $BilledID = $_SESSION['fisifmemberid'];
	      } else {
	          $BilledID = $user->user_login;
	      }

				$xx = new FISIF_Tools_Db();

        // grab the associated member data
        $member = $xx->getUserInfo('member', $BilledID);

        // Get the Agency
        $tablename = "agencies";
        $params = "WHERE `id`='$member[agencyid]' ";
        $ret = $xx->GetRecords("*", $tablename, $params);
        $agent = $ret[0];

        // account summary
        echo "<div class=\"section-fisifhome-summary\">\n";

				if (!$BilledID){
					echo "<div class=\"section-fisifhome-left\">\n";
					echo "<h4>Member Billing ID Undefined!</h4>\n";
					echo "<p>Please select a Member on the Agents Home page.</p>\n";
					echo "</div>\n";
				} else {
					// Display Member Summary
					$ssm_style = "background: none repeat scroll 0 0 #898989; background-color: #898989; ";
					$ssm_style .= "color: #FFFFFF; font: normal 400 12px/1 'times_new_roman', 'open_sansregular', Arial, sans-serif;";
					$ssm_style .= "padding: 5px; margin: 0;";
	        echo "<div class=\"section-fisifhome-left\">\n";
					echo "<h4 style=\"$ssm_style\">Member</h4>\n";
					echo "<p><b>$member[name] ($member[id])</b><br/>\n";
					echo "$member[address]<br />\n";
					echo "$member[city] $member[zip]<br />\n";
					echo "Phone: $member[phone]<br />\n";
					echo "Fax: $member[fax]<br/>\n";
					echo "</div>\n";

					// Display Agent Summary
					echo "<div class=\"section-fisifhome-right\">\n";
					echo "<h4 style=\"$ssm_style\">Agent</h4>\n";
					echo "<p><b>$agent[name]</b></br>\n";
					echo "$agent[address]<br />\n";
					echo "$agent[city] $agent[zip]<br />\n";
					echo "Phone: $agent[phone]<br />\n";
					echo "Fax: $agent[fax]</p></div>\n";

				}
				// end account summary
				echo "</div>\n";
    }

	/**
     * Display wrapper/callback for all reports
     *
     * @since    0.0.7
     * @access   public
     */
    public function displayReport($content) {

        // global display class for reports
        echo "<div class=\"section-fisif-report\">\n";
        echo $content;
        echo "</div>\n";

    }
}
