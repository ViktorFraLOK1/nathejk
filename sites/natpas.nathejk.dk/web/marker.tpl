            <header>
                <h1>Interessepunkt</h1>
            </header>
            
            <section class="fancyboxContent">
            {*
                <h1>Another important header of some sorts</h1>
                
                <div class="systemMessage">
                    <p class="systemMessageHeader">System error: something right here</p>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
                </div>
                
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
              *}  
                <h2>Interessepunkt til kort</h2>
                <form action="" method="post">
                <fieldset class="formStandard">
                    <div class="formTextWrap">
                        <label for="f01">Marker</label>
                        <input type="text" class="formText" id="f01" name="title" value="{$marker->title|escape}" />
                    </div>
                    <div class="formTextareaWrap">
                        <label for="f02">Beskrivelse</label>
                        <textarea class="formTextarea" id="f02" name="description">{$marker->description|escape}</textarea>
                    </div>
                    <div class="formTextareaWrapDisabled">
                        <span class="pseudoLabel">Placering</span>
                        <div id="map_canvas" class="pseudoFormTextArea" style="width:340px;height:250px"></div>
                        <input type="hidden" name="value" value="{$marker->value|escape}" id="markerPosition" />
                    </div>
                    <!--div class="formSelectWrap">
                        <label for="f23">Production year:</label>
                        <select id="f23" class="formSelect">
                            <option>2011</option>
                            <option>2010</option>
                            <option>2009</option>
                            <option>2008</option>
                            <option>2007</option>
                        </select>
                    </div-->
                    <div class="formTextareaWrapDisabled">
                        <span class="pseudoLabel">Ikon</span>
                        <div class="pseudoFormTextArea" style="height:auto">
                            {foreach from=$iconNames item=iconName}
                            <input type="radio" class="formRadio" name="iconName" value="{$iconName|escape}" {if $iconName==$marker->iconName} checked="checked"{/if}/>
                            <img src="/gmap/icons/demo/{$iconName|escape}.png">
                            {/foreach}
                        </div>
                        <span class="formLink"><a href="/gmap/icons/demo/">se flere ikoner</a></span>
                    </div>
                    <div class="formTextareaWrapDisabled">
                        <span class="pseudoLabel">Farve</span>
                        <div class="pseudoFormTextArea" style="height:auto">
                            {foreach from=$colorNames item=colorCode key=colorName}
                            <div style="background:#{$colorCode};border:1px solid #999;width:30px; height:30px; float:left; text-align:center;margin:3px;">
                            <input type="radio" class="formRadio" name="colorName" value="{$colorName|escape}" {if $colorName==$marker->colorName} checked="checked"{/if} />
                            </div>
                            {/foreach}
                        </div>
                    </div>
                </fieldset>
                <fieldset class="formStandard">
                    <div class="formSubmitWrap">
                        <input type="submit" value="Gem" name="save" class="formSubmit" />
                        <input type="submit" value="Slet" name="delete" class="formSubmit" />
                        <span class="formLink"><a href="#" class="cancelLink">Afbryd</a></span>
                    </div>
                </fieldset>
                </form>
            </section>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
<script type="text/javascript">
  function initialize() {
    var latlng = new google.maps.LatLng({$marker->value|default:'55.57130605318969,11.951373046875005'|escape});
    var map = new google.maps.Map(document.getElementById("map_canvas"), {
      zoom: 7,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      streetViewControl: false,
      mapTypeControl: false,
      scrollwheel: false,
    });
    var marker = new google.maps.Marker({
        position: latlng,
        map: map,
        draggable: true,
    });
    google.maps.event.addListener(marker, "dragend", function(event) { 
        var lat = event.latLng.lat(); 
        var lng = event.latLng.lng(); 
        document.getElementById('markerPosition').value = event.latLng.lat() + ',' + event.latLng.lng();
    }); 
    var bounds = new google.maps.LatLngBounds();
    {foreach from=$markers item=marker}
    bounds.extend(new google.maps.LatLng({$marker->value|escape:'javascript'}));
    {/foreach}
    map.fitBounds(bounds);
  }
  initialize();
</script>

