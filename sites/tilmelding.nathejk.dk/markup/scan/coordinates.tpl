<html>
<head>
<title>Nathejk</title>
<script type="text/javascript">
 
  function getLocation(pos)
  {
    var latitde = pos.coords.latitude;
    var longitude = pos.coords.longitude;
    location.href = '?location=' + latitde + ':' + longitude;
  }
  function unknownLocation()
  {
        var coor = prompt('Indtast dit kortkoordinat:');
        if (coor) {
            location.href = '?location=' + coor;
        } else {
            alert('du mangler at opgive koordinat, din fangst er ikke registreret');
        }
  }
if (navigator && navigator.geolocation) {
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
