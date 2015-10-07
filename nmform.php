<html>
<head><script type="text/javascript" src="./js/calendarDateInput.js">

/***********************************************
* Jason's Date Input Calendar- By Jason Moon http://calendar.moonscript.com/dateinput.cfm
* Script featured on and available at http://www.dynamicdrive.com
* Keep this notice intact for use.
***********************************************/

</script>
</head>
<body>
<form action="./temp.php">
<input type=hidden name="new" value="true">
<?php
$DATE = date("m-d-Y");

echo "<script>DateInput('date', true, 'MON-DD-YYYY', '$DATE')</script>\n";
?>
<input type=submit value="Start New Month">
</form>
</body>
</html>
