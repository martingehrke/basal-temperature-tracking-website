<?php include('user.php'); $user->require_login(); ?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Monthly Basil Temperature Tracking</title>

<script type="text/javascript" src="./js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="./js/jquery-ui-1.7.1.custom.min.js"></script>

<link rel='stylesheet' href='./css/styles.css' type='text/css' media='all' />
<link rel='stylesheet' href='./css/box.css' type='text/css' media='all' />

<script type="text/javascript">
jQuery.fn.do_something = function (){
	$(this).toggleClass("ui-selected").siblings().removeClass("ui-selected");

	var temp = $(this).attr("value");
	var day = $(this).parent().attr("id");

	if ( $(this).attr("class") == "ui-selected" ){
		$("#info").load("./updatedb.php?action=add&temp=" + temp + "&day=" + day ); }
	else {
		$("#info").load("./updatedb.php?action=remove&temp=" + temp + "&day=" + day ); }
}

jQuery.fn.openbox = function (formtitle, data)
{
	var box = document.getElementById('box'); 
	document.getElementById('filter').style.display='block';
  
	var btitle = document.getElementById('boxtitle');
	btitle.innerHTML = "Day " + formtitle.substring(1);
  
	box.style.display='block';

	$("textarea").val(data);
	$("textarea").focus();
}	

jQuery.fn.closebox = function()
{
	document.getElementById('box').style.display='none';
	document.getElementById('filter').style.display='none';
}

jQuery.fn.enterNote = function(){
	var new_val = $("textarea").attr('value').replace(/\s/g,"%20");
	var day = document.getElementById('boxtitle').innerHTML.split(" ",2);

	$("#info").load("./updatedb.php?action=note&note=" + new_val + "&day=day" + day[1] );
	var TEMP = "#D" + day[1];
	$(TEMP).attr("name", new_val);
	console.debug(TEMP + " ~ embed" );
	console.debug($(TEMP + " ~ embed").attr('src') );
	$(TEMP + " ~ embed").attr('src', "./notes.php?notes=" + new_val);
	$(this).closebox();
}

  // When the document is ready set up our sortable with it's inherant function(s)
$(document).ready(function() {
	<?php
		$CYCLE_DAYS = 36;
		for ($DAY=1; $DAY <= $CYCLE_DAYS; $DAY++){
			print "$(\"#day$DAY\").children().click( function() { $(this).do_something(); });\n";
		}
	?>

	$(".cf").click( function() { 
		var PVALS=new Array("&nbsp","we","wc","d","s");
		var cur_val = $(this).attr("value")*1; //use identity of multiplication of 1 to cast as int
		var new_val = (cur_val+1)%PVALS.length;
		var day = $(this).attr("id");
		$("#info").load("./updatedb.php?action=cycle&cf=" + new_val + "&day=" + day ); 
		$(this).attr("value", new_val);
		$(this).html(PVALS[new_val]);
	});

	$(".inter").click( function() {
                var cur_val = $(this).attr("value")*1; //use identity of multiplication of 1 to cast as int
                var new_val = Math.abs(cur_val - 1);
                var day = $(this).attr("id");
                $("#info").load("./updatedb.php?action=sex&sex=" + new_val + "&day=" + day );
                $(this).attr("value", new_val);
		if (new_val){ $(this).text("X"); }
		else { $(this).html("&nbsp"); }
        });

	$(".notes > img").click( function() {  $(this).openbox($(this).attr('id'), $(this).attr('name')) });	


});

</script>

</head>
<body>
<?php

	function float_equal($f1, $f2) {
	   return (int)($f1*10) == (int)($f2*10);
	}

	$db = new PDO('sqlite:'.dirname(dirname(__FILE__)).'/basal/dbs/users.db');
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$USER_ID = $_SESSION['user']['id'];

	if (array_key_exists("new", $_REQUEST)){
		#user_id|month|wd_start|year|cycle_start_day|month_span
		$SPLIT = explode("-", $_REQUEST["new"]);
		$weekday = date("N", mktime(0,0,0,$SPLIT[0],$SPLIT[1],$SPLIT[2]));
		$stmt = $db->prepare("INSERT INTO months VALUES (?, ?, ?, ?, ?, 1)");
		$stmt->excute(array($USER_ID, $SPLIT[0], $weekday, $SPLIT[2], $SPLIT[2]));
		$CURRENT_MONTH = $SPLIT[0];
	}
	else {
		
		$CURRENT_MONTH = 7;
	}

	/*
	if ( array_key_exists('current_month', $_GET) ) {
		$CURRENT_MONTH = $_GET['current_month'];
	}
	else $CURRENT_MONTH = 5;
	*/

        $stmt = $db->prepare("SELECT temperature, day, cervical_fluid, sex,note FROM temperatures WHERE month = ? AND user_id = ? ORDER BY day ASC");
        $result = $stmt->execute(array($CURRENT_MONTH, $USER_ID));
	$TDATA = $stmt->fetchAll();

	$DATA = array();
	$CF_DATA = array();
	$SEX = array();
	$NOTES = array();
	foreach ($TDATA as $DPOINT){
		$DATA[$DPOINT[1]] = $DPOINT[0]/10;
		$CF_DATA[$DPOINT[1]] = $DPOINT[2];
		$SEX[$DPOINT[1]] = $DPOINT[3];
		$NOTES[$DPOINT[1]] = $DPOINT[4];
	}
	

	$stmt = $db->prepare("SELECT wd_start, cycle_start_day, month_span FROM months WHERE user_id = ? AND month = ?");
	$stmt->execute(array($USER_ID, $CURRENT_MONTH));
	$RESULT = $stmt->fetch();
	$DOW_START = $RESULT[0];
	$CYCLE_START_DAY = $RESULT[1];
	$MONTH_SPAN = $RESULT[2];

	//Which months is this cycle in
	$MONTHS = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

	$MONTH_HEADER = "";

	for ($M=$CURRENT_MONTH-1; $M < $CURRENT_MONTH+$MONTH_SPAN; $M++){
		$MONTH_HEADER .= $MONTHS[$M]." / "; 
	}

	print "<h2>".substr($MONTH_HEADER, 0, -2)."</h2>";

	//Day of the cycle
	print "<TABLE ><TR><TH>Cycle Day</TH>";
	for ($DAY=1; $DAY <= $CYCLE_DAYS; $DAY++){
		print "<TH>$DAY</TH>";
	}

	//day of the month
	print "</TR><TR><TH>Day of the Month</TH>";
	$days_in_month = cal_days_in_month(CAL_GREGORIAN, $CURRENT_MONTH, 2009) ; 
	for ($DAY=$CYCLE_START_DAY-1; $DAY < $CYCLE_DAYS+$CYCLE_START_DAY-1; $DAY++){
		print "<TH>".(($DAY%$days_in_month)+1)."</TH>";
	}

	//weekday
	print "</TR><TR><TH>Day of the Week</TH>";
	$DOW = array('Su','M','T','W','TR','F','Sa');
	for ($DAY=$DOW_START; $DAY < $CYCLE_DAYS+$DOW_START; $DAY++){
                print "<TH>".$DOW[$DAY%7]."</TH>";
        }

	//sex? yes please
	print "</TR><TR><TH>Intercourse</TH>\n";
	for ($DAY=1; $DAY <= $CYCLE_DAYS; $DAY++){
		$TS = $SEX[$DAY];
		if ($TS == 1)
	                print "<TH id=\"yad".($DAY)."\" value=\"$TS\" class=\"inter\">X</TH>";
		else
	                print "<TH id=\"yad".($DAY)."\" value=\"$TS\" class=\"inter\">&nbsp</TH>";
        }

	//daily temperature	
	print "</TR><TR><TD>Temperatures</TD>";
	for ($DAY=1; $DAY <= $CYCLE_DAYS ; $DAY++){
		print "<TD>";
		print "<UL id=\"day$DAY\">";
		for ($t=99.0; $t> 96.5; $t-=0.1){

			if (float_equal($t, $DATA[$DAY]))
				print ("<li class=\"ui-selected\" value=\"");
			else print ("<li value=\"");
	
			if($t-(int)$t>=.1)
				print( ($t*10)."\">".substr(($t-(int)$t), 2,1)."</li>");
			else print ($t*10)."\">$t</li>";

		}
		print "</UL>";
		print "</TD>\n";
	}

	//cervical fluid
	print "</TR><TR><TH>Cervical Fluid</TH>\n";
	$PCF = array('&nbsp','we','wc', 'd', 's'); //possible values

	
	
	for ($DAY=1; $DAY <= $CYCLE_DAYS; $DAY++){
		if ($CF_DATA[$DAY]) { $CFD = $CF_DATA[$DAY]; }
		else { $CFD = 0; }
                print "<TD id=\"yad".($DAY)."\" value=\"".($CFD)."\" class=\"cf\">".($PCF[$CFD])."</TD>\n";
        }	
	
	print "</TR><TR class=\"take_notes_tr\" style=\"height:40px\"><TH> <embed class=\"svgex\" src=\"./notes.php?notes=Notes\" type=\"image/svg+xml\" frameborder=\"no\" width=\"30\" height=\"60\" /> </TH>\n";
	
	for ($DAY=1; $DAY <= $CYCLE_DAYS; $DAY++){
	print "<TD valign=top ><div class=\"notes\"><img id=\"D$DAY\" name=\"$NOTES[$DAY]\" style=\"padding:2px\" src=\"./images/plusIcon.gif\"><br/> <embed class=\"svgex\" src=\"./notes.php?notes=$NOTES[$DAY]\" type=\"image/svg+xml\" frameborder=\"no\" width=\"14\" height=\"60\" /></div></TD>\n";
        }
	
	print "</TABLE>"
?>
<div id="info">HERE:
</div>
<a href="./nmform.php">New Month</a>
<div id="filter"></div>
<div id="box"></box>
    <span id="boxtitle"></span>
    <form method="GET" action="#" >
	<textarea name="notes" rows=5 cols=28></textarea><br/>
	<input type="button" name="submit" value="Submit" onclick="$(this).enterNote()">
	<input type="button" name="cancel" value="Cancel" onclick="$(this).closebox()">
    </form>
</div>
</div>
</body>
</html>
