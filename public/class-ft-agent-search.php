<?php

class FT_Agent_Search {


	public function __construct() {
       
	}

    function agentSearch() {
 
        $current_user = wp_get_current_user();
        $BilledID = $current_user->user_login;	
		$xx = new FISIF_Tools_Db();
        
        // grab the associated member data
        $member = $xx->getUserInfo('member', $BilledID);
	
        // Reset the search tool
        if (isset($_POST['agent_search_new'])) {
            unset($_POST['agent_search_string']);
        }
	
        if (isset($_POST['agent_search_string'])) {
            $string = $_POST['agent_search_string'];
		
            // Search by the City
			//$params = "WHERE `city` LIKE '%$string%' ";
			//$searchby = "city";
            
              if (is_numeric($string)) {
              //Search by the Zipcode
              $params = "WHERE `zip` LIKE '%$string%' ";
              $searchby = "zipcode";
              } else {
              // Search by the City
              $params = "WHERE `city` LIKE '%$string%' ";
              $searchby = "city";
              }

        } else {
            $params = "";
            $string = "";
        }
	
		echo "<h4>Enter a city or zipcode to narrow your search:</h4>\n";
        echo "<p><form action=\"\" method=\"POST\">\n";
		echo "<input name=\"agent_search_string\" type=\"text\" size=\"50\" maxlength=\"255\" value=\"$string\">\n";
		echo "<input type=\"submit\" value=\"Refine Search\" name=\"Refine\">\n";
		echo "<input type=\"submit\" value=\"Reset\" name=\"agent_search_new\">\n";
        echo "</form><br /></p>\n";

        $tablename = "agencies";
        $select_what = "*";	
        $agents = $xx->GetRecords($select_what, $tablename, $params);	
	
        if (!empty($agents)) {
            $currentStyle = "listing1";
            foreach ($agents as $key => $agent) {
			
                echo "<div class=\"$currentStyle\">\n";
				echo "<h4>$agent[name]</h4>\n";
				echo "<p>$agent[address] $agent[address2] $agent[city] $agent[state] $agent[zip]<br/>\n";
                if (!empty($agent[phone])) { echo "Phone: $agent[phone]"; }
                if (!empty($agent[fax])) { echo " - Fax: $agent[fax]"; }
				echo "</p>\n";
                echo "</div>\n";
			
                switch ($currentStyle) {
				case "listing1":
					$currentStyle = "listing2";
					break;
				case "listing2":
					$currentStyle = "listing3";
					break;
				case "listing3":
					$currentStyle = "listing1";
					break;
                }
			
                // print_r($agent);
                // echo "<br /><br />\n";
            }
        } else {
            echo "<p>No agents were found in the $searchby that you entered</p>\n";
        }
    }
}

 
