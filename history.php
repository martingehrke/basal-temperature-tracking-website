<?php include('user.php'); $user->require_login(); ?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Monthly Basil Temperature Tracking</title>

</head>
<body>
<?php

	$db = new PDO('sqlite:'.dirname(dirname(__FILE__)).'/basal/dbs/users.db');
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$USER_ID = $_SESSION['user']['id'];

        $stmt = $db->prepare("SELECT month, day, temperature FROM temperatures WHERE user_id = ? ORDER BY month, day ASC");
        $result = $stmt->execute(array($USER_ID));
	$TDATA = $stmt->fetchAll();
	
	$MONTHS = array();

	foreach ($TDATA as $DPOINT){
		if (array_key_exists($DPOINT[0], $MONTHS))
			$MONTHS[$DPOINT[0]][$DPOINT[1]] = $DPOINT[2];
		else{
			$MONTHS[$DPOINT[0]] = array();
			$MONTHS[$DPOINT[0]][$DPOINT[1]] = $DPOINT[2];
		}
	}

	$RDATA = array();
	$KEYS = array_keys($MONTHS);
	foreach ($KEYS as $MONTH){
		$RDATA[$MONTH][0] = "";
		for ($i=1; $i<37; $i++){
			if (array_key_exists($i, $MONTHS[$MONTH])){
				if (is_numeric($MONTHS[$MONTH][$i]) && ($MONTHS[$MONTH][$i] != 0) ){
					$RDATA[$MONTH][$i] = $MONTHS[$MONTH][$i]/10;}
				else $RDATA[$MONTH][$i] = "";
			}
			else 
				$RDATA[$MONTH][$i] = "";
		}
	}

	//Which months is this cycle in
	$MNAME = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

	// Standard inclusions      
	include("pChart/pData.class");   
	include("pChart/pChart.class");   
  
	// Dataset definition    
	$DataSet = new pData;   

	foreach ($KEYS as $MONTH){
		$DataSet->AddPoint($RDATA[$MONTH],"Serie$MONTH");	
	}

	$DataSet->AddAllSeries(); 
	$DataSet->SetAbsciseLabelSerie();   
	foreach ($KEYS as $MONTH){
                $DataSet->SetSerieName($MNAME[$MONTH],"Serie$MONTH");
        }
	
	$DataSet->SetYAxisName("Temperature");
	$DataSet->SetXAxisName("Cycle Day");
	$DataSet->SetYAxisUnit("°");

	// Initialise the graph   
	$Test = new pChart(900,500);   
	$Test->setFontProperties("pChart/Fonts/tahoma.ttf",8);   
	$Test->setGraphArea(70,30,850,450);   
	$Test->drawFilledRoundedRectangle(7,7,900,500,5,240,240,240);   
	$Test->drawRoundedRectangle(5,5,900,500,5,230,230,230);   
	$Test->drawGraphArea(255,255,255,TRUE);

	$Test->setFixedScale(96.5,99);
	$Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,3);   
	$Test->drawGrid(4,TRUE,230,230,230,50);

	// Draw the 0 line   
	$Test->setFontProperties("pChart/Fonts/tahoma.ttf",6);   
	$Test->drawTreshold(0,143,55,72,TRUE,TRUE);   

	// Draw the line graph
	$Test->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());   
	$Test->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,2,255,255,255);   

	// Finish the graph   
	$Test->setFontProperties("pChart/Fonts/tahoma.ttf",8);   
	$Test->drawLegend(75,35,$DataSet->GetDataDescription(),255,255,255);   
	$Test->setFontProperties("pChart/Fonts/tahoma.ttf",10);   
	$Test->drawTitle(300,22,"Temperature Comparison",50,50,50,585);   
	$Test->Render("charts/example1.png");      
?>
<img src="charts/example1.png">
</body>
</html>
