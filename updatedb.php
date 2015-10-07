<?php include('user.php'); $user->require_login(); ?>
<?php

$db = new PDO('sqlite:'.dirname(dirname(__FILE__)).'/basil/dbs/users.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$USER_ID = $_SESSION['user']['id'];
$CURRENT_MONTH = 7;
$DAY = substr($_GET['day'],3,2);
$TEMPERATURE = $_GET['temp'];
$ACTION = $_GET['action'];

echo $DAY;
echo $$_GET['note'];

if ($ACTION == 'add'){
	$stmt = $db->prepare("UPDATE temperatures SET temperature = ? WHERE user_id = ? AND day = ? AND month = ?");
        $stmt->execute(array($TEMPERATURE, $USER_ID, $DAY, $CURRENT_MONTH));

	if ( ! $stmt->rowCount() ) {
		$stmt = $db->prepare("INSERT INTO temperatures (day, temperature, month, user_id, cervical_fluid, sex, note) values (?, ?, ?, ?, 0, 0, '')");
		$result = $stmt->execute(array($DAY, $TEMPERATURE, $CURRENT_MONTH, $USER_ID));
	}
}

else if ($ACTION == 'remove') {
	$stmt = $db->prepare("UPDATE temperatures SET temperature = 0 WHERE day = ? AND temperature = ? AND month = ? AND user_id = ?");
	$stmt->execute(array($DAY, $TEMPERATURE, $CURRENT_MONTH, $USER_ID));
}

else if ($ACTION == 'cycle'){
	$stmt = $db->prepare("UPDATE temperatures SET cervical_fluid = ? WHERE user_id = ? AND day = ? AND month = ?");
	$stmt->execute(array($_GET['cf'], $USER_ID, $DAY, $CURRENT_MONTH));

	if ( ! $stmt->rowCount() ) {
		$stmt = $db->prepare("INSERT INTO temperatures (day, temperature, month, user_id, cervical_fluid, sex, note) values (?, 0, ?, ?, ?, 0, '')");	
	        $result = $stmt->execute(array($DAY, $CURRENT_MONTH, $USER_ID, $_GET['cf']));
	}
}

else if ($ACTION == 'sex'){
        $stmt = $db->prepare("UPDATE temperatures SET sex = ? WHERE user_id = ? AND day = ? AND month = ?");
        $stmt->execute(array($_GET['sex'], $USER_ID, $DAY, $CURRENT_MONTH));

        if ( ! $stmt->rowCount() ) {
                $stmt = $db->prepare("INSERT INTO temperatures (day, temperature, month, user_id, cervical_fluid, sex, note) values (?, 0, ?, ?, 0, ?, '')");
                $result = $stmt->execute(array($DAY, $CURRENT_MONTH, $USER_ID, $_GET['sex']));
        }
}

else if ($ACTION == 'note'){
	
        $stmt = $db->prepare("UPDATE temperatures SET note = ? WHERE user_id = ? AND day = ? AND month = ?");
        $stmt->execute(array($_GET['note'], $USER_ID, $DAY, $CURRENT_MONTH));

	print $_GET['note'];

        if ( ! $stmt->rowCount() ) {
                $stmt = $db->prepare("INSERT INTO temperatures (day, temperature, month, user_id, cervical_fluid, sex, note) values (?, 0, ?, ?, 0, 0, ?)");
                $result = $stmt->execute(array($DAY, $CURRENT_MONTH, $USER_ID, $_GET['note']));
        }
}


?>
