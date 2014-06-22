<?php
$patruljeId = isset($_GET['n']) ? $_GET['n'] : 0;
$cookie = isset($_COOKIE['nh']) ? $_COOKIE['nh'] : ':';
list($seniorId, $checksum) = explode(':', $cookie);


    $capture = new Nathejk_CheckIn;
    $capture->teamId = $patruljeId;
    $capture->createdUts = time();
    $capture->typeName = 'qr-fail';
    $capture->remark = "FAILED scanning: [{$_SERVER['REMOTE_ADDR']}][{$cookie}] {$_SERVER['HTTP_USER_AGENT']}";
    $capture->save();


Pasta_Http::exitWithRedirect("/scan/$patruljeId:123");

if (isset($_GET['location'])) {
    $capture = new Nathejk_CheckIn;
    $capture->teamId = $patruljeId;
    $capture->memberId = $senior->id;
    $capture->location = $_GET['location'];
    $capture->createdUts = time();
    $capture->typeName = 'qr';
    $capture->save();
    $team = $capture->team;
    $text = '';
    if ($team->activeMemberCount != $team->startMemberCount) {
        $text = '<h2 style="color:red">OBS: Patruljen er reduceret til ' . $team->activeMemberCount . ' spejdere</h2>';
    }
    die('<h1>Du har fanget "' . $capture->team->teamNumber . '. ' . $capture->team->title. '"</h1><p>De er nu blevet fanget <b>' . $capture->team->catchCount . '</b> gange' . $text);
}
if (!$senior->isBandit()) {
    Pasta_Http::exitWithRedirect("http://tilmelding.nathejk.dk/qr.php?n=$patruljeId&location=unknown");
}
?>
<html>
<head>
<title> Know your current location </title>
<script type="text/javascript">
 
  function getLocation(pos)
  {
    var latitde = pos.coords.latitude;
    var longitude = pos.coords.longitude;
    location.href = 'http://tilmelding.nathejk.dk/qr.php?n=<?php print $patruljeId; ?>&location=' + latitde + ':' + longitude;
    //alert('Your current coordinates (latitide,longitude) are : ' + latitde + ', ' + longitude);
  }
  function unknownLocation()
  {
        var coor = prompt('Indtast dit kortkoordinat:');
        if (coor) {
            location.href = 'http://tilmelding.nathejk.dk/qr.php?n=<?php print $patruljeId; ?>&location=' + coor;
        } else {
            alert('du mangler at opgive koordinat, din fangst er ikke registreret');
        }
  }
if (navigator.geolocation) {
  navigator.geolocation.getCurrentPosition(getLocation, unknownLocation);
} else {
  unknownLocation();
}
</script>
</head>
<body>
<button onclick="unknownLocation()">Tryk her</button>


</body>
</html>
