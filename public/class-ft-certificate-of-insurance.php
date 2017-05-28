<?php
class FT_Certificate_Of_Insurance extends FISIF_Tools_Public {

	public function __construct() {

	}

    function certificateOfInsurance() {

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

        // Get the Certificate data for this Member
        $tablename = "members_coi";
        $params = "WHERE `CorpBilledID`='$BilledID' ";
        $result = $xx->GetRecords("*", $tablename, $params);
				$membercoi = $result[0];

        $the_date = date('F d, Y');

				// place output in buffer
				ob_start();

        echo "<div class=\"section-fisif-report\">\n";

        echo "<h4 align=\"center\">Food Industry Insurance Fund of New Mexico</h4>\n";
        echo "<p align=\"center\">P.O. Box 14710\n";
        echo "<br/>Albuquerque, NM 87191-4710</p>\n";
        echo "<p align=\"center\">$the_date</p>\n";
        echo "<hr/>\n";
				echo "<p align=\"center\">CorpBilledID : $membercoi[CorpBilledID]</p>\n";
				echo "<p align=\"center\">BilledName : $membercoi[BilledName]</p>\n";
				echo "<p align=\"center\">YearCode : $membercoi[YearCode]</p>\n";
				echo "<p align=\"center\">Endorsements : $membercoi[Endorsements]</p>\n";
				echo "<p align=\"center\">PolicyLimit : $membercoi[PolicyLimit]</p>\n";
				echo "<p align=\"center\">MemberAddr1 : $membercoi[MemberAddr1]</p>\n";
				echo "<p align=\"center\">MemberAddr2 : $membercoi[MemberAddr2]</p>\n";
				echo "<p align=\"center\">MemberCityLine : $membercoi[MemberCityLine]</p>\n";
				echo "<p align=\"center\">MemberPhone : $membercoi[MemberPhone]</p>\n";
				echo "<p align=\"center\">MemberFax : $membercoi[MemberFax]</p>\n";
				echo "<p align=\"center\">BeginDate : $membercoi[BeginDate]</p>\n";
				echo "<p align=\"center\">EndDate : $membercoi[EndDate]</p>\n";
				echo "<p align=\"center\">AgencyName : $membercoi[AgencyName]</p>\n";
				echo "<p align=\"center\">AgencyAddr1 : $membercoi[AgencyAddr1]</p>\n";
				echo "<p align=\"center\">AgencyAddr2 : $membercoi[AgencyAddr2]</p>\n";
				echo "<p align=\"center\">AgencyCityLine : $membercoi[AgencyCityLine]</p>\n";
				echo "<p align=\"center\">AgencyPhone : $membercoi[AgencyPhone]</p>\n";
				echo "<p align=\"center\">AgencyFax : $membercoi[AgencyFax]</p>\n";
				echo "<p align=\"center\">CoverageBLiability : $membercoi[CoverageBLiability]</p>\n";
				echo "<p align=\"center\">AggregateLimit : $membercoi[AggregateLimit]</p>\n";
				echo "<hr/>\n";

				$html_output = ob_get_clean();
				$report_fn = 'certificate_of_insurance-'.$BilledID;
				$report_title = 'Certificate of Insurance';

				//$_SESSION['fisiftools'] = '';
				//$_SESSION['fisiftools']['report_html'] = $html_output;
				//$_SESSION['fisiftools']['report_fn'] = $report_fn;
				//$_SESSION['fisiftools']['report_title'] = $report_title;
				$this->generateCertificateFromImport($report_fn, $membercoi);
				//return $html_output;
    }
		public function generateCertificateFromImport($report_fn, $membercoi){

				// set layout parameters for the Members Billed ID
				$lo_mbid = [
					'font' => 'Times',
					'font_size' => '11',
					'x' => '20',
					'y' => '46'
				];

				// set layout parameters for the Policy Period
				$lo_pp = [
					'font' => 'Times',
					'font_size' => '11',
					'x' => '69',
					'from_y' => '40',
					'to_y' => '49'
				];

				// set layout parameters for the Member Insured and Address
				$lo_mi = [
					'font' => 'Times',
					'font_size' => '11',
					'x' => '10',
					'y' => '72',
					'lineoffset' => '5'
				];

				// set layout parameters for the Agency Name and Address
				$lo_ag = [
					'font' => 'Times',
					'font_size' => '11',
					'x' => '112',
					'y' => '72',
					'lineoffset' => '5'
				];

				// set layout parameters for Body Injury by Incident
				$lo_biba = [
					'font' => 'Times',
					'font_size' => '11',
					'x' => '139',
					'y' => '150'
				];

				// set layout parameters for Body Injury by Disease
				$lo_bibd = [
					'font' => 'Times',
					'font_size' => '11',
					'x' => '139',
					'y' => '158'
				];

				// set layout parameters for Body Injury by Disease - Policy Limit
				$lo_bibd_limit = [
					'font' => 'Times',
					'font_size' => '11',
					'x' => '139',
					'y' => '166'
				];

				// set layout parameters for Endorsements
				$lo_endorse = [
					'font' => 'Times',
					'font_size' => '11',
					'x' => '14',
					'y' => '195'
				];

				// set layout parameters for Coverage B Liability
				$lo_cbl = [
					'font' => 'Times',
					'font_size' => '11',
					'x' => '175',
					'y' => '236'
				];

				// set layout parameters for Aggregate Limit
				$lo_al = [
					'font' => 'Times',
					'font_size' => '11',
					'x' => '175',
					'y' => '253'
				];

				$pdf_import_file = FT_PDF_IMPORTDIR . 'CERT_OF_INSURANCE.pdf';

				// initiate FPDI
				$cert = new FPDI();
				$cert->SetTextColor(0, 0, 0);
				// add a page
				$cert->AddPage();
				// set the source file
				$cert->setSourceFile($pdf_import_file);
				// import page 1
				$tplIdx = $cert->importPage(1);
				$size = $cert->getTemplateSize($tplIdx);
				$cert->useTemplate($tplIdx, null, null, $size['w'], 310, FALSE);

				// Member Billed ID
				$cert->SetFont($lo_mbid[font]);
				$cert->SetFontSize($lo_mbid[font_size]);
				$cert->SetXY($lo_mbid[x], $lo_mbid[y]);
				$cert->Write(0, $membercoi[CorpBilledID]);

				// Policy Period
				$cert->SetFont($lo_pp[font]);
				$cert->SetFontSize($lo_pp[font_size]);
				$cert->SetXY($lo_pp[x], $lo_pp[from_y]);
				$cert->Write(0, $membercoi[BeginDate]." 12:01AM");
				$cert->SetXY($lo_pp[x], $lo_pp[to_y]);
				$cert->Write(0, $membercoi[EndDate]." 12:01AM");

				// Member Insured and Address
				$cert->SetFont($lo_mi[font]);
				$cert->SetFontSize($lo_mi[font_size]);
				$cert->SetXY($lo_mi[x], $lo_mi[y]);
				$cert->Write(0, $membercoi[BilledName]);
				$cert->SetXY($lo_mi[x], $lo_mi[y] + $lo_mi[lineoffset]);
				$cert->Write(0, $membercoi[MemberAddr1]);
				$cert->SetXY($lo_mi[x], $lo_mi[y] + ($lo_mi[lineoffset] * 2));
				$cert->Write(0, $membercoi[MemberAddr2]);
				$cert->SetXY($lo_mi[x], $lo_mi[y] + ($lo_mi[lineoffset] * 3));
				$cert->Write(0, $membercoi[MemberCityLine]);
				$cert->SetXY($lo_mi[x], $lo_mi[y] + ($lo_mi[lineoffset] * 4));
				$cert->Write(0, $membercoi[MemberPhone]);
				$cert->SetXY($lo_mi[x], $lo_mi[y] + ($lo_mi[lineoffset] * 5));
				$cert->Write(0, $membercoi[MemberFax]);

				// Agency Name and Address
				$cert->SetFont($lo_ag[font]);
				$cert->SetFontSize($lo_ag[font_size]);
				$cert->SetXY($lo_ag[x], $lo_ag[y]);
				$cert->Write(0, $membercoi[AgencyName]);
				$cert->SetXY($lo_ag[x], $lo_ag[y] + $lo_ag[lineoffset]);
				$cert->Write(0, $membercoi[AgencyAddr1]);
				$cert->SetXY($lo_ag[x], $lo_ag[y] + ($lo_ag[lineoffset] * 2));
				$cert->Write(0, $membercoi[AgencyAddr2]);
				$cert->SetXY($lo_ag[x], $lo_ag[y] + ($lo_ag[lineoffset] * 3));
				$cert->Write(0, $membercoi[AgencyCityLine]);
				$cert->SetXY($lo_ag[x], $lo_ag[y] + ($lo_ag[lineoffset] * 4));
				$cert->Write(0, $membercoi[AgencyPhone]);
				$cert->SetXY($lo_ag[x], $lo_ag[y] + ($lo_ag[lineoffset] * 5));
				$cert->Write(0, $membercoi[AgencyFax]);

				// Bodily Injury By Accident
				$cert->SetFont($lo_biba[font]);
				$cert->SetFontSize($lo_biba[font_size]);
				$cert->SetXY($lo_biba[x], $lo_biba[y]);
				$cert->Write(0, number_format($membercoi[PolicyLimit], 0));

				// Bodily Injury By Disease
				$cert->SetFont($lo_bibd[font]);
				$cert->SetFontSize($lo_bibd[font_size]);
				$cert->SetXY($lo_bibd[x], $lo_bibd[y]);
				$cert->Write(0, number_format($membercoi[PolicyLimit], 0));

				// Bodily Injury By Disease - Policy Limit
				$cert->SetFont($lo_bibd_limit[font]);
				$cert->SetFontSize($lo_bibd_limit[font_size]);
				$cert->SetXY($lo_bibd_limit[x], $lo_bibd_limit[y]);
				$cert->Write(0, number_format($membercoi[PolicyLimit], 0));

				// Endorsements
				$cert->SetFont($lo_endorse[font]);
				$cert->SetFontSize($lo_endorse[font_size]);
				$cert->SetXY($lo_endorse[x], $lo_endorse[y]);
				$cert->Write(0, $membercoi[Endorsements]);;

				// Coverage B Liability - hardcoded in template
				//$cert->SetFont($lo_cbl[font]);
				//$cert->SetFontSize($lo_cbl[font_size]);
				//$cert->SetXY($lo_cbl[x], $lo_cbl[y]);
				//$cert->Write(0, number_format($membercoi[CoverageBLiability], 0));

				// Aggregate Limit - hardcoded in template
				//$cert->SetFont($lo_al[font]);
				//$cert->SetFontSize($lo_al[font_size]);
				//$cert->SetXY($lo_al[x], $lo_al[y]);
				//$cert->Write(0, number_format($membercoi[AggregateLimit], 0));

				// set path for new PDF to be created
				$filename = $report_fn.'-'.date('dMY').'.pdf';
				$pdf_export_final_pdf = FT_PDF_EXPORTDIR.'pdf/'.$filename;

				// write the file to disk on the server
				$cert->Output($pdf_export_final_pdf,"F");

				unset($cert);
			  gc_collect_cycles();

				// return the finalized file
				header('Content-type: application/pdf');
				header('Content-Disposition: attachment; filename="' . $filename . '"');
				header('Content-Transfer-Encoding: binary');
				header('Content-Length: ' . filesize($pdf_export_final_pdf));
				header('Accept-Ranges: bytes');
				readfile($pdf_export_final_pdf);
				exit();
		}
}
?>
