<?php
require_once('tcpdf_include.php');
session_start();
require_once '../settings/connection.php';
require_once '../settings/filter.php';


if(!isset($_SESSION['user_identity']) || !isset($_SESSION['faculty']))
{
	header("location: ../pull_out.php");
}
		
		$date500 = new DateTime("Now");
		$J = date_format($date500,"D");
		$Q = date_format($date500,"d-F-Y, h:i:s A");
		$dateprint = "Printed On: ".$J.", ".$Q;	

		
// create new PDF document

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF 
{
	// Page footer
		public function Footer() {
		// Position at 15 mm from bottom
		$this->SetY(-15);
		// Set font
		$this->SetFont('dejavusans', 'I', 8);
		// Page number
		$this->Cell(0, 10, 'A T B U System - 2015 - - Page '.$this->getAliasNumPage().' Of '.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
		
		//getAliasNumPage() from the immediate line mean the current page
		//getAliasNbPages() from the immediate line mean the total number of pages
		//remember you can remove them and put a common string there
	}
}



$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Abdulraheem Sherif A');
$pdf->SetTitle('A T B U System');
$pdf->SetSubject('Courses');

$pdf->SetKeywords('Pesoka, Computers, Nigeia, Limited, Ajaokuta');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// to remove default header use this
$pdf->setPrintHeader(false);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->SetFont('dejavusans', '', 10);

// add a page - 100 level
$pdf->AddPage();
// set alpha to semi-transparency


$html = '<table cellspacing="0" cellpadding="1" border="0" align="center">
    <tr >
        <td rowspan="4" width="90"><img src="images/image_demo.jpg" width="200" height="200"/></td>
        <td width="460"></td>
        <td rowspan="4" width="90"><img src="images/image_demo.jpg" width="200" height="200"/></td>
    </tr>
    <tr >
        <td  align="center" style="font-size:15;font-weight:bold;color:blue" >Abubakar Tafawa Balewa University, Bauchi</td>
    </tr>
    <tr >
    	 <td align="center" style="font-size:11;font-weight:bold">P.M.B	1037 Bauchi State, Nigeria.</td>
    </tr>
    <tr>
       <td align="center"  style="font-size:10;font-weight:bold;color:black">LIST OF ALL COURSES - 100 LEVEL</td>
    </tr>

</table>';
$pdf->writeHTML($html, true, false, true, false, '');

$html ='<table cellspacing="0" cellpadding="1" border="0" align="center">
    <tr style="bottom-border:1 solid;">
		<td align="Left" style="font-size:8;font-weight:bold;color:brown"> 100 LEVEL - FIRST SEMESTER </td> 
		<td  align="Right" style="font-size:8;">'.$dateprint.'</td> 
    </tr>
</table><hr>';

$pdf->writeHTML($html, true, false, false, false, '');


// -----------PERSONALINFORMATION GOODS DETAIL TABLE----------------------------------------------
$pdf->SetAlpha(0.3);
$img_file = K_PATH_IMAGES.'image_demo.jpg';
$pdf->Image($img_file, 55, 85, 100, 100, '', '', '', false, 300, '', false, false, 0);
$pdf->SetAlpha(1);

$html1 ="";
$stmt = $conn->prepare("SELECT * FROM atbu_course where Level=? AND Semester =? ORDER BY Course_Code ASC");
$stmt->execute(array("100","1"));
if ($stmt->rowCount () >= 1)
{
	 $html1 .= '<tr style="background-color:grey;color:yellow;">
			<td width="40">S/N<u>o</u></td>
			<td width="100">Course Code </td>
			<td width="200"> Course Title</td>
			<td width="100">Course Unit </td>
			<td width="200">Lecturer</td>
		</tr>';
		$id=1;
		$staff_name ="";$tot_unit=0;
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) 
		{
			
			$Add_Id=$row['staff_id'];
			$stmt2 = $conn->prepare("SELECT  staff_name, staff_id FROM atbu_staff where staff_id=?");
			$stmt2->execute(array($Add_Id));
			if ($stmt2->rowCount () >= 1)
			{
				$rows = $stmt2->fetch(PDO::FETCH_ASSOC);
				$staff_name = $rows['staff_name'];
			}
			
			$tot_unit = $tot_unit + $row['course_unit'];	
			
			$Course_Title = $row['Course_Title'];
			$Course_Code = $row['Course_Code'];	
			$course_unit = $row['course_unit'];	
			$html1 .= '<tr  >
							<td >'.$id.'</td>
							<td>'.$Course_Code.'</td>
							<td>'.$Course_Title.'</td>
							<td>'.$course_unit.'</td>
							<td>'.$staff_name.'</td>
						</tr>
						<tr width="400">
							<td align="Center" colspan ="5" style="font-size:8;"></td>
						</tr>';
						$id = $id + 1;
		}
		$id = $id -1;
		$html  = '<table border="1" cellpadding="4" width="800">'.$html1.'
		<tr>
		<td></td>
			<td style="text-align:right;color:blue" >N<u>o</u> of Courses :</td>
			<td style="text-align:left;">'.$id.'</td>
			
			<td style="text-align:right;color:blue" >Total Unit :</td>
			<td style="text-align:left;">'.$tot_unit.'</td>
		</tr></table>';
		// output the HTML content
		$pdf->writeHTML($html, true, false, false, false, '');
}
$pdf->AddPage();///100 level second semester

$html ='<table cellspacing="0" cellpadding="1" border="0" align="center">
    <tr style="bottom-border:1 solid;">
		<td align="Left" style="font-size:8;font-weight:bold;color:brown"> 100 LEVEL - SECOND SEMESTER </td> 
		<td  align="Right" style="font-size:8;"></td> 
    </tr>
</table><hr>';

$pdf->writeHTML($html, true, false, false, false, '');


// -----------PERSONALINFORMATION GOODS DETAIL TABLE----------------------------------------------
$pdf->SetAlpha(0.3);
$img_file = K_PATH_IMAGES.'image_demo.jpg';
$pdf->Image($img_file, 55, 85, 100, 100, '', '', '', false, 300, '', false, false, 0);
$pdf->SetAlpha(1);

$html1 ="";
$stmt = $conn->prepare("SELECT * FROM atbu_course where Level=? AND Semester =? ORDER BY Course_Code ASC");
$stmt->execute(array("100","2"));
if ($stmt->rowCount () >= 1)
{
	 $html1 .= '<tr style="background-color:grey;color:yellow;">
			<td width="40">S/N<u>o</u></td>
			<td width="100">Course Code </td>
			<td width="200"> Course Title</td>
			<td width="100">Course Unit </td>
			<td width="200">Lecturer</td>
		</tr>';
		$id=1;
		$staff_name ="";$tot_unit=0;
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) 
		{
			
			$Add_Id=$row['staff_id'];
			$stmt2 = $conn->prepare("SELECT  staff_name, staff_id FROM atbu_staff where staff_id=?");
			$stmt2->execute(array($Add_Id));
			if ($stmt2->rowCount () >= 1)
			{
				$rows = $stmt2->fetch(PDO::FETCH_ASSOC);
				$staff_name = $rows['staff_name'];
			}
			
			$tot_unit = $tot_unit + $row['course_unit'];	
			
			$Course_Title = $row['Course_Title'];
			$Course_Code = $row['Course_Code'];	
			$course_unit = $row['course_unit'];	
			$html1 .= '<tr  >
							<td >'.$id.'</td>
							<td>'.$Course_Code.'</td>
							<td>'.$Course_Title.'</td>
							<td>'.$course_unit.'</td>
							<td>'.$staff_name.'</td>
						</tr>
						<tr width="400">
							<td align="Center" colspan ="5" style="font-size:8;"></td>
						</tr>';
						$id = $id + 1;
		}
		$id = $id -1;
		$html  = '<table border="1" cellpadding="4" width="800">'.$html1.'
		<tr>
		<td></td>
			<td style="text-align:right;color:blue" >N<u>o</u> of Courses :</td>
			<td style="text-align:left;">'.$id.'</td>
			
			<td style="text-align:right;color:blue" >Total Unit :</td>
			<td style="text-align:left;">'.$tot_unit.'</td>
		</tr></table>';
		// output the HTML content
		$pdf->writeHTML($html, true, false, false, false, '');
}

//2000 level
// add a page - 200 level
$pdf->AddPage();
// set alpha to semi-transparency


$html = '<table cellspacing="0" cellpadding="1" border="0" align="center">
    <tr >
        <td rowspan="4" width="90"><img src="images/image_demo.jpg" width="200" height="200"/></td>
        <td width="460"></td>
        <td rowspan="4" width="90"><img src="images/image_demo.jpg" width="200" height="200"/></td>
    </tr>
    <tr >
        <td  align="center" style="font-size:15;font-weight:bold;color:blue" >Abubakar Tafawa Balewa University, Bauchi</td>
    </tr>
    <tr >
    	 <td align="center" style="font-size:11;font-weight:bold">P.M.B	1037 Bauchi State, Nigeria.</td>
    </tr>
    <tr>
        <td align="center"  style="font-size:10;font-weight:bold;color:black">LIST OF ALL COURSES - 200 LEVEL</td>
    </tr>

</table>';
$pdf->writeHTML($html, true, false, true, false, '');

$html ='<table cellspacing="0" cellpadding="1" border="0" align="center">
    <tr style="bottom-border:1 solid;">
		<td align="Left" style="font-size:8;font-weight:bold;color:brown"> 200 LEVEL - FIRST SEMESTER </td> 
		<td  align="Right" style="font-size:8;">'.$dateprint.'</td> 
    </tr>
</table><hr>';

$pdf->writeHTML($html, true, false, false, false, '');


// -----------PERSONALINFORMATION GOODS DETAIL TABLE----------------------------------------------
$pdf->SetAlpha(0.3);
$img_file = K_PATH_IMAGES.'image_demo.jpg';
$pdf->Image($img_file, 55, 85, 100, 100, '', '', '', false, 300, '', false, false, 0);
$pdf->SetAlpha(1);

$html1 ="";
$stmt = $conn->prepare("SELECT * FROM atbu_course where Level=? AND Semester =? ORDER BY Course_Code ASC");
$stmt->execute(array("200","1"));
if ($stmt->rowCount () >= 1)
{
	 $html1 .= '<tr style="background-color:grey;color:yellow;">
			<td width="40">S/N<u>o</u></td>
			<td width="100">Course Code </td>
			<td width="200"> Course Title</td>
			<td width="100">Course Unit </td>
			<td width="200">Lecturer</td>
		</tr>';
		$id=1;
		$staff_name ="";$tot_unit=0;
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) 
		{
			
			$Add_Id=$row['staff_id'];
			$stmt2 = $conn->prepare("SELECT  staff_name, staff_id FROM atbu_staff where staff_id=?");
			$stmt2->execute(array($Add_Id));
			if ($stmt2->rowCount () >= 1)
			{
				$rows = $stmt2->fetch(PDO::FETCH_ASSOC);
				$staff_name = $rows['staff_name'];
			}
			
			$tot_unit = $tot_unit + $row['course_unit'];	
			
			$Course_Title = $row['Course_Title'];
			$Course_Code = $row['Course_Code'];	
			$course_unit = $row['course_unit'];	
			$html1 .= '<tr  >
							<td >'.$id.'</td>
							<td>'.$Course_Code.'</td>
							<td>'.$Course_Title.'</td>
							<td>'.$course_unit.'</td>
							<td>'.$staff_name.'</td>
						</tr>
						<tr width="400">
							<td align="Center" colspan ="5" style="font-size:8;"></td>
						</tr>';
						$id = $id + 1;
		}
		$id = $id -1;
		$html  = '<table border="1" cellpadding="4" width="800">'.$html1.'
		<tr>
		<td></td>
			<td style="text-align:right;color:blue" >N<u>o</u> of Courses :</td>
			<td style="text-align:left;">'.$id.'</td>
			
			<td style="text-align:right;color:blue" >Total Unit :</td>
			<td style="text-align:left;">'.$tot_unit.'</td>
		</tr></table>';
		// output the HTML content
		$pdf->writeHTML($html, true, false, false, false, '');
}
$pdf->AddPage();///100 level second semester

$html ='<table cellspacing="0" cellpadding="1" border="0" align="center">
    <tr style="bottom-border:1 solid;">
		<td align="Left" style="font-size:8;font-weight:bold;color:brown"> 200 LEVEL - SECOND SEMESTER </td> 
		<td  align="Right" style="font-size:8;"></td> 
    </tr>
</table><hr>';

$pdf->writeHTML($html, true, false, false, false, '');


// -----------PERSONALINFORMATION GOODS DETAIL TABLE----------------------------------------------
$pdf->SetAlpha(0.3);
$img_file = K_PATH_IMAGES.'image_demo.jpg';
$pdf->Image($img_file, 55, 85, 100, 100, '', '', '', false, 300, '', false, false, 0);
$pdf->SetAlpha(1);

$html1 ="";
$stmt = $conn->prepare("SELECT * FROM atbu_course where Level=? AND Semester =? ORDER BY Course_Code ASC");
$stmt->execute(array("200","2"));
if ($stmt->rowCount () >= 1)
{
	 $html1 .= '<tr style="background-color:grey;color:yellow;">
			<td width="40">S/N<u>o</u></td>
			<td width="100">Course Code </td>
			<td width="200"> Course Title</td>
			<td width="100">Course Unit </td>
			<td width="200">Lecturer</td>
		</tr>';
		$id=1;
		$staff_name ="";$tot_unit=0;
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) 
		{
			
			$Add_Id=$row['staff_id'];
			$stmt2 = $conn->prepare("SELECT  staff_name, staff_id FROM atbu_staff where staff_id=?");
			$stmt2->execute(array($Add_Id));
			if ($stmt2->rowCount () >= 1)
			{
				$rows = $stmt2->fetch(PDO::FETCH_ASSOC);
				$staff_name = $rows['staff_name'];
			}
			
			$tot_unit = $tot_unit + $row['course_unit'];	
			
			$Course_Title = $row['Course_Title'];
			$Course_Code = $row['Course_Code'];	
			$course_unit = $row['course_unit'];	
			$html1 .= '<tr  >
							<td >'.$id.'</td>
							<td>'.$Course_Code.'</td>
							<td>'.$Course_Title.'</td>
							<td>'.$course_unit.'</td>
							<td>'.$staff_name.'</td>
						</tr>
						<tr width="400">
							<td align="Center" colspan ="5" style="font-size:8;"></td>
						</tr>';
						$id = $id + 1;
		}
		$id = $id -1;
		$html  = '<table border="1" cellpadding="4" width="800">'.$html1.'
		<tr>
		<td></td>
			<td style="text-align:right;color:blue" >N<u>o</u> of Courses :</td>
			<td style="text-align:left;">'.$id.'</td>
			
			<td style="text-align:right;color:blue" >Total Unit :</td>
			<td style="text-align:left;">'.$tot_unit.'</td>
		</tr></table>';
		// output the HTML content
		$pdf->writeHTML($html, true, false, false, false, '');
}

//################## 300 ###################################///
//300 Level
// add a page - 300 level
$pdf->AddPage();
// set alpha to semi-transparency


$html = '<table cellspacing="0" cellpadding="1" border="0" align="center">
    <tr >
        <td rowspan="4" width="90"><img src="images/image_demo.jpg" width="200" height="200"/></td>
        <td width="460"></td>
        <td rowspan="4" width="90"><img src="images/image_demo.jpg" width="200" height="200"/></td>
    </tr>
    <tr >
        <td  align="center" style="font-size:15;font-weight:bold;color:blue" >Abubakar Tafawa Balewa University, Bauchi</td>
    </tr>
    <tr >
    	 <td align="center" style="font-size:11;font-weight:bold">P.M.B	1037 Bauchi State, Nigeria.</td>
    </tr>
    <tr>
        <td align="center"  style="font-size:10;font-weight:bold;color:black">LIST OF ALL COURSES - 300 LEVEL</td>
    </tr>

</table>';
$pdf->writeHTML($html, true, false, true, false, '');

$html ='<table cellspacing="0" cellpadding="1" border="0" align="center">
    <tr style="bottom-border:1 solid;">
		<td align="Left" style="font-size:8;font-weight:bold;color:brown"> 300 LEVEL - FIRST SEMESTER </td> 
		<td  align="Right" style="font-size:8;">'.$dateprint.'</td> 
    </tr>
</table><hr>';

$pdf->writeHTML($html, true, false, false, false, '');


// -----------PERSONALINFORMATION GOODS DETAIL TABLE----------------------------------------------
$pdf->SetAlpha(0.3);
$img_file = K_PATH_IMAGES.'image_demo.jpg';
$pdf->Image($img_file, 55, 85, 100, 100, '', '', '', false, 300, '', false, false, 0);
$pdf->SetAlpha(1);

$html1 ="";
$stmt = $conn->prepare("SELECT * FROM atbu_course where Level=? AND Semester =? ORDER BY Course_Code ASC");
$stmt->execute(array("300","1"));
if ($stmt->rowCount () >= 1)
{
	 $html1 .= '<tr style="background-color:grey;color:yellow;">
			<td width="40">S/N<u>o</u></td>
			<td width="100">Course Code </td>
			<td width="200"> Course Title</td>
			<td width="100">Course Unit </td>
			<td width="200">Lecturer</td>
		</tr>';
		$id=1;
		$staff_name ="";$tot_unit=0;
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) 
		{
			
			$Add_Id=$row['staff_id'];
			$stmt2 = $conn->prepare("SELECT  staff_name, staff_id FROM atbu_staff where staff_id=?");
			$stmt2->execute(array($Add_Id));
			if ($stmt2->rowCount () >= 1)
			{
				$rows = $stmt2->fetch(PDO::FETCH_ASSOC);
				$staff_name = $rows['staff_name'];
			}
			
			$tot_unit = $tot_unit + $row['course_unit'];	
			
			$Course_Title = $row['Course_Title'];
			$Course_Code = $row['Course_Code'];	
			$course_unit = $row['course_unit'];	
			$html1 .= '<tr  >
							<td >'.$id.'</td>
							<td>'.$Course_Code.'</td>
							<td>'.$Course_Title.'</td>
							<td>'.$course_unit.'</td>
							<td>'.$staff_name.'</td>
						</tr>
						<tr width="400">
							<td align="Center" colspan ="5" style="font-size:8;"></td>
						</tr>';
						$id = $id + 1;
		}
		$id = $id -1;
		$html  = '<table border="1" cellpadding="4" width="800">'.$html1.'
		<tr>
		<td></td>
			<td style="text-align:right;color:blue" >N<u>o</u> of Courses :</td>
			<td style="text-align:left;">'.$id.'</td>
			
			<td style="text-align:right;color:blue" >Total Unit :</td>
			<td style="text-align:left;">'.$tot_unit.'</td>
		</tr></table>';
		// output the HTML content
		$pdf->writeHTML($html, true, false, false, false, '');
}
$pdf->AddPage();///300 level second semester

$html ='<table cellspacing="0" cellpadding="1" border="0" align="center">
    <tr style="bottom-border:1 solid;">
		<td align="Left" style="font-size:8;font-weight:bold;color:brown"> 300 LEVEL - SECOND SEMESTER </td> 
		<td  align="Right" style="font-size:8;"></td> 
    </tr>
</table><hr>';

$pdf->writeHTML($html, true, false, false, false, '');


// -----------PERSONALINFORMATION GOODS DETAIL TABLE----------------------------------------------
$pdf->SetAlpha(0.3);
$img_file = K_PATH_IMAGES.'image_demo.jpg';
$pdf->Image($img_file, 55, 85, 100, 100, '', '', '', false, 300, '', false, false, 0);
$pdf->SetAlpha(1);

$html1 ="";
$stmt = $conn->prepare("SELECT * FROM atbu_course where Level=? AND Semester =? ORDER BY Course_Code ASC");
$stmt->execute(array("300","2"));
if ($stmt->rowCount () >= 1)
{
	 $html1 .= '<tr style="background-color:grey;color:yellow;">
			<td width="40">S/N<u>o</u></td>
			<td width="100">Course Code </td>
			<td width="200"> Course Title</td>
			<td width="100">Course Unit </td>
			<td width="200">Lecturer</td>
		</tr>';
		$id=1;
		$staff_name ="";$tot_unit=0;
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) 
		{
			
			$Add_Id=$row['staff_id'];
			$stmt2 = $conn->prepare("SELECT  staff_name, staff_id FROM atbu_staff where staff_id=?");
			$stmt2->execute(array($Add_Id));
			if ($stmt2->rowCount () >= 1)
			{
				$rows = $stmt2->fetch(PDO::FETCH_ASSOC);
				$staff_name = $rows['staff_name'];
			}
			
			$tot_unit = $tot_unit + $row['course_unit'];	
			
			$Course_Title = $row['Course_Title'];
			$Course_Code = $row['Course_Code'];	
			$course_unit = $row['course_unit'];	
			$html1 .= '<tr  >
							<td >'.$id.'</td>
							<td>'.$Course_Code.'</td>
							<td>'.$Course_Title.'</td>
							<td>'.$course_unit.'</td>
							<td>'.$staff_name.'</td>
						</tr>
						<tr width="400">
							<td align="Center" colspan ="5" style="font-size:8;"></td>
						</tr>';
						$id = $id + 1;
		}
		$id = $id -1;
		$html  = '<table border="1" cellpadding="4" width="800">'.$html1.'
		<tr>
		<td></td>
			<td style="text-align:right;color:blue" >N<u>o</u> of Courses :</td>
			<td style="text-align:left;">'.$id.'</td>
			
			<td style="text-align:right;color:blue" >Total Unit :</td>
			<td style="text-align:left;">'.$tot_unit.'</td>
		</tr></table>';
		// output the HTML content
		$pdf->writeHTML($html, true, false, false, false, '');
}

//################## 400 ###################################///
//400 Level
// add a page - 400 level
$pdf->AddPage();
// set alpha to semi-transparency


$html = '<table cellspacing="0" cellpadding="1" border="0" align="center">
    <tr >
        <td rowspan="4" width="90"><img src="images/image_demo.jpg" width="200" height="200"/></td>
        <td width="460"></td>
        <td rowspan="4" width="90"><img src="images/image_demo.jpg" width="200" height="200"/></td>
    </tr>
    <tr >
        <td  align="center" style="font-size:15;font-weight:bold;color:blue" >Abubakar Tafawa Balewa University, Bauchi</td>
    </tr>
    <tr >
    	 <td align="center" style="font-size:11;font-weight:bold">P.M.B	1037 Bauchi State, Nigeria.</td>
    </tr>
    <tr>
       <td align="center"  style="font-size:10;font-weight:bold;color:black">LIST OF ALL COURSES - 400 LEVEL</td>
    </tr>

</table>';
$pdf->writeHTML($html, true, false, true, false, '');

$html ='<table cellspacing="0" cellpadding="1" border="0" align="center">
    <tr style="bottom-border:1 solid;">
		<td align="Left" style="font-size:8;font-weight:bold;color:brown"> 400 LEVEL - FIRST SEMESTER </td> 
		<td  align="Right" style="font-size:8;">'.$dateprint.'</td> 
    </tr>
</table><hr>';

$pdf->writeHTML($html, true, false, false, false, '');


// -----------PERSONALINFORMATION GOODS DETAIL TABLE----------------------------------------------
$pdf->SetAlpha(0.3);
$img_file = K_PATH_IMAGES.'image_demo.jpg';
$pdf->Image($img_file, 55, 85, 100, 100, '', '', '', false, 300, '', false, false, 0);
$pdf->SetAlpha(1);

$html1 ="";
$stmt = $conn->prepare("SELECT * FROM atbu_course where Level=? AND Semester =? ORDER BY Course_Code ASC");
$stmt->execute(array("400","1"));
if ($stmt->rowCount () >= 1)
{
	 $html1 .= '<tr style="background-color:grey;color:yellow;">
			<td width="40">S/N<u>o</u></td>
			<td width="100">Course Code </td>
			<td width="200"> Course Title</td>
			<td width="100">Course Unit </td>
			<td width="200">Lecturer</td>
		</tr>';
		$id=1;
		$staff_name ="";$tot_unit=0;
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) 
		{
			
			$Add_Id=$row['staff_id'];
			$stmt2 = $conn->prepare("SELECT  staff_name, staff_id FROM atbu_staff where staff_id=?");
			$stmt2->execute(array($Add_Id));
			if ($stmt2->rowCount () >= 1)
			{
				$rows = $stmt2->fetch(PDO::FETCH_ASSOC);
				$staff_name = $rows['staff_name'];
			}
			
			$tot_unit = $tot_unit + $row['course_unit'];	
			
			$Course_Title = $row['Course_Title'];
			$Course_Code = $row['Course_Code'];	
			$course_unit = $row['course_unit'];	
			$html1 .= '<tr  >
							<td >'.$id.'</td>
							<td>'.$Course_Code.'</td>
							<td>'.$Course_Title.'</td>
							<td>'.$course_unit.'</td>
							<td>'.$staff_name.'</td>
						</tr>
						<tr width="400">
							<td align="Center" colspan ="5" style="font-size:8;"></td>
						</tr>';
						$id = $id + 1;
		}
		$id = $id -1;
		$html  = '<table border="1" cellpadding="4" width="800">'.$html1.'
		<tr>
		<td></td>
			<td style="text-align:right;color:blue" >N<u>o</u> of Courses :</td>
			<td style="text-align:left;">'.$id.'</td>
			
			<td style="text-align:right;color:blue" >Total Unit :</td>
			<td style="text-align:left;">'.$tot_unit.'</td>
		</tr></table>';
		// output the HTML content
		$pdf->writeHTML($html, true, false, false, false, '');
}
$pdf->AddPage();///300 level second semester

$html ='<table cellspacing="0" cellpadding="1" border="0" align="center">
    <tr style="bottom-border:1 solid;">
		<td align="Left" style="font-size:8;font-weight:bold;color:brown"> 400 LEVEL - SECOND SEMESTER </td> 
		<td  align="Right" style="font-size:8;"></td> 
    </tr>
</table><hr>';

$pdf->writeHTML($html, true, false, false, false, '');


// -----------PERSONALINFORMATION GOODS DETAIL TABLE----------------------------------------------
$pdf->SetAlpha(0.3);
$img_file = K_PATH_IMAGES.'image_demo.jpg';
$pdf->Image($img_file, 55, 85, 100, 100, '', '', '', false, 300, '', false, false, 0);
$pdf->SetAlpha(1);

$html1 ="";
$stmt = $conn->prepare("SELECT * FROM atbu_course where Level=? AND Semester =? ORDER BY Course_Code ASC");
$stmt->execute(array("400","2"));
if ($stmt->rowCount () >= 1)
{
	 $html1 .= '<tr style="background-color:grey;color:yellow;">
			<td width="40">S/N<u>o</u></td>
			<td width="100">Course Code </td>
			<td width="200"> Course Title</td>
			<td width="100">Course Unit </td>
			<td width="200">Lecturer</td>
		</tr>';
		$id=1;
		$staff_name ="";$tot_unit=0;
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) 
		{
			
			$Add_Id=$row['staff_id'];
			$stmt2 = $conn->prepare("SELECT  staff_name, staff_id FROM atbu_staff where staff_id=?");
			$stmt2->execute(array($Add_Id));
			if ($stmt2->rowCount () >= 1)
			{
				$rows = $stmt2->fetch(PDO::FETCH_ASSOC);
				$staff_name = $rows['staff_name'];
			}
			
			$tot_unit = $tot_unit + $row['course_unit'];	
			
			$Course_Title = $row['Course_Title'];
			$Course_Code = $row['Course_Code'];	
			$course_unit = $row['course_unit'];	
			$html1 .= '<tr  >
							<td >'.$id.'</td>
							<td>'.$Course_Code.'</td>
							<td>'.$Course_Title.'</td>
							<td>'.$course_unit.'</td>
							<td>'.$staff_name.'</td>
						</tr>
						<tr width="400">
							<td align="Center" colspan ="5" style="font-size:8;"></td>
						</tr>';
						$id = $id + 1;
		}
		$id = $id -1;
		$html  = '<table border="1" cellpadding="4" width="800">'.$html1.'
		<tr>
		<td></td>
			<td style="text-align:right;color:blue" >N<u>o</u> of Courses :</td>
			<td style="text-align:left;">'.$id.'</td>
			
			<td style="text-align:right;color:blue" >Total Unit :</td>
			<td style="text-align:left;">'.$tot_unit.'</td>
		</tr></table>';
		// output the HTML content
		$pdf->writeHTML($html, true, false, false, false, '');
}
//################## 500 ###################################///
//500 Level
// add a page - 500 level
$pdf->AddPage();
// set alpha to semi-transparency


$html = '<table cellspacing="0" cellpadding="1" border="0" align="center">
    <tr >
        <td rowspan="4" width="90"><img src="images/image_demo.jpg" width="200" height="200"/></td>
        <td width="460"></td>
        <td rowspan="4" width="90"><img src="images/image_demo.jpg" width="200" height="200"/></td>
    </tr>
    <tr >
        <td  align="center" style="font-size:15;font-weight:bold;color:blue" >Abubakar Tafawa Balewa University, Bauchi</td>
    </tr>
    <tr >
    	 <td align="center" style="font-size:11;font-weight:bold">P.M.B	1037 Bauchi State, Nigeria.</td>
    </tr>
    <tr>
       <td align="center"  style="font-size:10;font-weight:bold;color:black">LIST OF ALL COURSES - 500 LEVEL</td>
    </tr>

</table>';
$pdf->writeHTML($html, true, false, true, false, '');

$html ='<table cellspacing="0" cellpadding="1" border="0" align="center">
    <tr style="bottom-border:1 solid;">
		<td align="Left" style="font-size:8;font-weight:bold;color:brown"> 500 LEVEL - FIRST SEMESTER </td> 
		<td  align="Right" style="font-size:8;">'.$dateprint.'</td> 
    </tr>
</table><hr>';

$pdf->writeHTML($html, true, false, false, false, '');


// -----------PERSONALINFORMATION GOODS DETAIL TABLE----------------------------------------------
$pdf->SetAlpha(0.3);
$img_file = K_PATH_IMAGES.'image_demo.jpg';
$pdf->Image($img_file, 55, 85, 100, 100, '', '', '', false, 300, '', false, false, 0);
$pdf->SetAlpha(1);

$html1 ="";
$stmt = $conn->prepare("SELECT * FROM atbu_course where Level=? AND Semester =? ORDER BY Course_Code ASC");
$stmt->execute(array("500","1"));
if ($stmt->rowCount () >= 1)
{
	 $html1 .= '<tr style="background-color:grey;color:yellow;">
			<td width="40">S/N<u>o</u></td>
			<td width="100">Course Code </td>
			<td width="200"> Course Title</td>
			<td width="100">Course Unit </td>
			<td width="200">Lecturer</td>
		</tr>';
		$id=1;
		$staff_name ="";$tot_unit=0;
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) 
		{
			
			$Add_Id=$row['staff_id'];
			$stmt2 = $conn->prepare("SELECT  staff_name, staff_id FROM atbu_staff where staff_id=?");
			$stmt2->execute(array($Add_Id));
			if ($stmt2->rowCount () >= 1)
			{
				$rows = $stmt2->fetch(PDO::FETCH_ASSOC);
				$staff_name = $rows['staff_name'];
			}
			
			$tot_unit = $tot_unit + $row['course_unit'];	
			
			$Course_Title = $row['Course_Title'];
			$Course_Code = $row['Course_Code'];	
			$course_unit = $row['course_unit'];	
			$html1 .= '<tr  >
							<td >'.$id.'</td>
							<td>'.$Course_Code.'</td>
							<td>'.$Course_Title.'</td>
							<td>'.$course_unit.'</td>
							<td>'.$staff_name.'</td>
						</tr>
						<tr width="400">
							<td align="Center" colspan ="5" style="font-size:8;"></td>
						</tr>';
						$id = $id + 1;
		}
		$id = $id -1;
		$html  = '<table border="1" cellpadding="4" width="800">'.$html1.'
		<tr>
		<td></td>
			<td style="text-align:right;color:blue" >N<u>o</u> of Courses :</td>
			<td style="text-align:left;">'.$id.'</td>
			
			<td style="text-align:right;color:blue" >Total Unit :</td>
			<td style="text-align:left;">'.$tot_unit.'</td>
		</tr></table>';
		// output the HTML content
		$pdf->writeHTML($html, true, false, false, false, '');
}
$pdf->AddPage();///300 level second semester

$html ='<table cellspacing="0" cellpadding="1" border="0" align="center">
    <tr style="bottom-border:1 solid;">
		<td align="Left" style="font-size:8;font-weight:bold;color:brown"> 500 LEVEL - SECOND SEMESTER </td> 
		<td  align="Right" style="font-size:8;"></td> 
    </tr>
</table><hr>';

$pdf->writeHTML($html, true, false, false, false, '');


// -----------PERSONALINFORMATION GOODS DETAIL TABLE----------------------------------------------
$pdf->SetAlpha(0.3);
$img_file = K_PATH_IMAGES.'image_demo.jpg';
$pdf->Image($img_file, 55, 85, 100, 100, '', '', '', false, 300, '', false, false, 0);
$pdf->SetAlpha(1);

$html1 ="";
$stmt = $conn->prepare("SELECT * FROM atbu_course where Level=? AND Semester =? ORDER BY Course_Code ASC");
$stmt->execute(array("500","2"));
if ($stmt->rowCount () >= 1)
{
	 $html1 .= '<tr style="background-color:grey;color:yellow;">
			<td width="40">S/N<u>o</u></td>
			<td width="100">Course Code </td>
			<td width="200"> Course Title</td>
			<td width="100">Course Unit </td>
			<td width="200">Lecturer</td>
		</tr>';
		$id=1;
		$staff_name ="";$tot_unit=0;
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) 
		{
			
			$Add_Id=$row['staff_id'];
			$stmt2 = $conn->prepare("SELECT  staff_name, staff_id FROM atbu_staff where staff_id=?");
			$stmt2->execute(array($Add_Id));
			if ($stmt2->rowCount () >= 1)
			{
				$rows = $stmt2->fetch(PDO::FETCH_ASSOC);
				$staff_name = $rows['staff_name'];
			}
			
			$tot_unit = $tot_unit + $row['course_unit'];	
			
			$Course_Title = $row['Course_Title'];
			$Course_Code = $row['Course_Code'];	
			$course_unit = $row['course_unit'];	
			$html1 .= '<tr  >
							<td >'.$id.'</td>
							<td>'.$Course_Code.'</td>
							<td>'.$Course_Title.'</td>
							<td>'.$course_unit.'</td>
							<td>'.$staff_name.'</td>
						</tr>
						<tr width="400">
							<td align="Center" colspan ="5" style="font-size:8;"></td>
						</tr>';
						$id = $id + 1;
		}
		$id = $id -1;
		$html  = '<table border="1" cellpadding="4" width="800">'.$html1.'
		<tr>
		<td></td>
			<td style="text-align:right;color:blue" >N<u>o</u> of Courses :</td>
			<td style="text-align:left;">'.$id.'</td>
			
			<td style="text-align:right;color:blue" >Total Unit :</td>
			<td style="text-align:left;">'.$tot_unit.'</td>
		</tr></table>';
		// output the HTML content
		$pdf->writeHTML($html, true, false, false, false, '');
}
$file_name = 'course_list';
$pdf->Output($file_name.'.pdf', 'I');
//============================================================+
// END OF FILE
//============================================================+

