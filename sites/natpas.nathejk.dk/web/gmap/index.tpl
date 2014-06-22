<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
<script type="text/javascript" src="geoxmlv320120831.js"></script>
<script type="text/javascript" src="http://geoxml3.googlecode.com/svn/branches/polys/geoxml3.js"></script>
<script type="text/javascript">
  var markers = {
    'places': [
    {
        'name': 'fre. kl. 21:09',
        'location': [55.5, 10.5]
    },
    {
        'name': 'Lør. kl. 03:01',
        'location': [55.38, 10.6]
    }]
  }
  function initialize() {
    var latlng = new google.maps.LatLng(55.397, 10.644);
    var myOptions = {
      zoom: 8,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    var myLatLng = new google.maps.LatLng(55.257037,12.388029);
    var beachMarker1 = new google.maps.Marker({
        position: myLatLng,
        map: map,
    //                              icon: image
    });
    var    position = new google.maps.LatLng(55.315349,11.966343);
    var beachMarker2 = new google.maps.Marker({
        position: position,
        map: map,
    //                              icon: image
    });
    var bounds = new google.maps.LatLngBounds();

var infowindow = new google.maps.InfoWindow({
    content: 'Se mor jeg har fanget en frakkel!',
    });

{foreach from=$spot item=s}
    position = new google.maps.LatLng({$s->location|replace:':':','});
    
    {assign var=color value='black'}
    {if $s->member and $s->team}
    {assign var=team value=$s->member->team}
    {if $team->lokNumber == 1}
        {assign var=color value=lightgreen}
    {/if}
    {if $team->lokNumber == 2}
        {assign var=color value=blue}
    {/if}
    {if $team->lokNumber == 3}
        {assign var=color value=yellow}
    {/if}
    {if $team->lokNumber == 4}
        {assign var=color value=purple}
    {/if}
    {if $team->lokNumber == 5}
        {assign var=color value=cyan}
    {/if}


    marker = new google.maps.Marker({ position: position,  title:"Hello World!", map: map, icon:'icon.image.php?label={$s->team->teamNumber}&color={$color}'});
    google.maps.event.addListener(marker, 'click', function() {
      infowindow.open(map, this);
      infowindow.setContent(this.title);
    });
    bounds.extend(position);
    {/if}
{/foreach}

{foreach from=$markers item=marker}
    position = new google.maps.LatLng({$marker->value|escape:'javascript'});
    //marker = new google.maps.Marker({ position: position,  title:"{$marker->title|escape:'javascript'}", map: map, icon:'{$marker->iconUrl}'});
    //bounds.extend(position);
{/foreach}
    map.fitBounds(bounds);
/*
    mgr = new MarkerManager(map, { trackMarkers: true, maxZoom: 15});
    google.maps.event.addListener(mgr, 'loaded', function() {
        //mgr.addMarkers(markers.countries, 0, 5);
        mgr.addMarkers(markers.places, 0, 11);
        //mgr.addMarkers(markers.locations, 12);
        mgr.refresh();
    });*/
filename = "NathejkOutline2012.kmz";
filename = 'doc.kml';
geoXml = new geoXML3.parser({
    map: map,
    singleInfoWindow: true,
    afterParse: useTheData
});
geoXml.parse(filename); 
geoXml.showDocument();  
    map.fitBounds(bounds);
  }
function useTheData()
{ }
/*
geoxml = new GeoXml("geoxml", map, "https://dl.dropbox.com/u/1479995/NathejkOutline2012.kmz", { 
    publishdirectory:"http://www.dyasdesigns.com/tntmap/",
    iwwidth:240,
    dohilite:false,
    sidebarid:"mysidebar"
});

geoxml.parse("Mountrushmoore Trip");  
*/
</script>
<div id="map_canvas" style="width:100%; height:100%"></div>
<script type="text/javascript">initialize()</script>

