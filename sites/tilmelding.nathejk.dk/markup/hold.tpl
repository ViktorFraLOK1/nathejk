{extends file="page.tpl"}
{block name=titleText}{$agenda->title|escape}{/block}
{block name=container}
    <div class="container">
      <div class="page-header">
        <h1>Tilmelding <small>indtast deltageroplysninger</small></h1>
      </div>
      <form  enctype="multipart/form-data" id="teamForm" class="form-horizontal {if $team->typeName == 'patrulje'}showSpejder{else}showSenior{/if}" action="" method="post">
        <div class="row">
          <div class="span6">
            <div class="well">
              <fieldset id="team">
                <legend><img src="/assets/icon/glyphicons_060_compass.png"> <span class="senior">Klanoplysninger</span><span class="spejder">Patruljeoplysninger</span></legend>
                <div class="control-group">
                  <label class="control-label"><span class="senior">Klan</span><span class="spejder">Patrulje</span></label>
                  <div class="controls">
                    <input class="span3" name="title" value="{$team->title|escape}" type="text">
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label">Gruppe og division</label>
                  <div class="controls">
                    <input class="span3" name="gruppe" value="{$team->gruppe|escape}" type="text">
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label">Korps</label>
                  <div class="controls">
                    {html_options options=$team->allKorps selected=$team->korps name="korps" class="span3" id="teamKorps"}
                  </div>
                </div>
                <div class="control-group spejder">
                  <label class="control-label">Evt. Liga-ID</label>
                  <div class="controls">
                    <input class="span3" name="ligaNumber" value="{$team->ligaNumber|escape}" type="text"{if $team->ligaNumberVerified} readonly{/if}>
                    <p class="help-block">Læs mere om LigaID og tilmeld jer Adventurespejdligaen her: <a href="http://liga.adventurespejd.dk/">liga.adventurespejd.dk</a>.</p>
                    
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label">Antal deltagere</label>
                  <div class="controls">
                  {for $i = $team->minMemberCount to $team->maxMemberCount}
                    <label class="radio inline">
                      <input name="memberCount" value="{$i}" type="radio"{if $team->typeName != 'patrulje' && $team->signupStatusTypeName=='PAID'} disabled="disabled"{/if}{if $team->memberCount == $i} checked="checked"{/if}> {$i}
                    </label>
                  {/for}
                  </div>
                </div>
              </fieldset>
            </div> <!-- /well -->
          </div> <!-- /span6 -->
          <div class="span6">
            <div class="alert alert-block" style="display:none">
              <fieldset>
                <legend><img src="/assets/icon/glyphicons_283_t-shirt.png"> <span>Adventurespejd Ligaen</span></legend>
                <p style="font-size:14px">Ligaen indeholder mange løb med nye udfordringer som du og din patrulje kan afprøve.</p>
                <ul>
                <li>Alligatorløbet</li>
                <li>Sværdkamp</li>
                <li>Solaris</li>
                </ul>
              </fieldset>
            </div>
          </div> <!-- /span6 -->
        </div> <!-- /row -->
        <div class="row">
          <div class="span12">

            <div class="well">
              <fieldset>
                <legend><img src="/assets/icon/glyphicons_024_parents.png"> Deltagere</legend>
                <table class="table" id="participants">
                  <thead>
                    <tr>
                      <th>Deltager</th>
                      <th>Adresse</th>
                      <th>Postnr. / by</th>
                      <th>E-mail</th>
                      <th class="ownPhone">Eget&nbsp;telefon</th>
                      <th class="spejder contactPhone">Forældretlf.</th>
                      <th>Fødselsdag</th>
                      <th>Deltaget&nbsp;før</th>
                      <th class="senior">Billede</th>
                    </tr>
                  </thead>
                  <tbody>
                  {for $i = 0 to $team->maxMemberCount - 1}
                    {if $i < $team->members|count}
                        {assign var=member value=$team->members.$i}
                    {else}
                        {assign var=member value=false}
                    {/if}
                    <tr class="member">
                      <td><input type="text" class="span2" name="members[{$i}][title]"{if $member} id="memberTitleInput{$member->id|escape}"{/if} placeholder="navn" value="{if $member}{$member->title|escape}{/if}"></td>
                      <td><input type="text" class="span2" name="members[{$i}][address]" placeholder="adresse" value="{if $member}{$member->address|escape}{/if}"></td>
                      <td><input type="text" class="span2" name="members[{$i}][postalCode]" placeholder="nummer" value="{if $member}{$member->postalCode|escape}{/if}"></td>
                      <td><input type="text" class="span2" name="members[{$i}][mail]" placeholder="adresse" value="{if $member}{$member->mail|escape}{/if}"></td>
                      <td class="ownPhone"><input type="text" class="span1" name="members[{$i}][phone]" placeholder="nummer" value="{if $member}{$member->phone|escape}{/if}"></td>
                      <td class="spejder contactPhone"><input type="text" class="span1" name="members[{$i}][contactPhone]" placeholder="nummer" value="{if $member}{$member->contactPhone|escape}{/if}"></td>
                      <td>
                      <!-- input type="text" class="span1" name="members[{$i}][birthDate]" placeholder="yyyy-mm-dd" value="{if $member}{$member->birthDate|escape}{/if}"-->
                        <div class="input-append date datepicker" style="padding:0;" id="dpYears{$i}" data-date="{if $member && $member->birthDate|intval}{$member->birthDate|escape}{/if}" data-date-format="yyyy-mm-dd" data-date-viewmode="years">
                            <span class="add-on"><i class="icon-calendar"></i></span><input name="members[{$i}][birthDate]" class="span1" size="16" type="text" style="font-size:10px" value="{if $member && $member->birthDate|intval}{$member->birthDate|escape}{/if}" readonly>
                        </div>
                      </td>
                      <td style="text-align:center">
                        <input name="members[{$i}][returning]" value="1" type="checkbox"{if $member && $member->returning} checked="checked"{/if}> Ja
                        <input name="members[{$i}][id]" value="{if $member}{$member->id|escape}{else}0{/if}" type="hidden">
                      </td>
                      <td class="senior">
                        {if $member}
                        <button class="btn btn-small{if !$member->photoId} btn-success{/if}" data-toggle="modal" data-target="#modal{$i}" data-remote="/upload/{if $member}{$member->teamId|escape}:{$member->team->checksum|escape}:{$member->id|escape}{/if}" type="button">{if $member->photoId}<i class="icon-ok"></i> skift{else}<i class="icon-user icon-white"></i> upload{/if}</button>
                        {/if}
                      </td>
                    </tr>
                  {/for}
                  {if $team->typeName == 'patrulje'}
                    <tr>
                        <td colspan=5 class="ownPhone" style="text-align:right">Mobilnummer på Nathejk (kun hvis telefon medbringes)</td>
                        <td class="contactPhone"></td>
                        <td colspan=2></td>
                    </tr>
                    <tr>
                        <td colspan=6 class="contactPhone" style="text-align:right">Forældres telefonnummer – Nathejk skal kunne kontakte dette nummer undervejs på løbet, hvis situationen kræver det</td>
                        <td colspan=2></td>
                    </tr>
                  {/if}
                  </tbody>
                </table>
              </fieldset>
            </div>

            <!-- Modal -->
            {for $i = 0 to $team->maxMemberCount - 1}
            <div id="modal{$i}" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel">Upload billede af:</h3>
            </div>
            <div class="modal-body">
            <p></p>
            </div>
            <div class="modal-footer">
            <button class="btn" type="button" data-dismiss="modal" aria-hidden="true">Afbryd</button>
            <button class="btn btn-primary" type="button" data-dismiss="modal" aria-hidden="true">Ok</button>
            </div>
            </div>
            {/for}

          </div>
        </div>
        <div class="row">
          {if $team->typeName == 'patrulje'}
          <div class="span6">
            <div class="well">
              <fieldset id="contact">
                <legend><img src="/assets/icon/glyphicons_003_user.png"> Kontaktperson under Nathejk</legend>
                <p style="font-size:14px">Kontaktpersonen er meget vigtig og skal være en person, som kender patruljen godt (fx tropslederen). Nathejks team skal kunne få fat i kontaktpersonen undervejs på løbet, hvis situationen kræver det.</p>
                <div class="control-group">
                  <label class="control-label">Navn</label>
                  <div class="controls">
                    <input class="span3" name="contactTitle"  value="{$team->contactTitle|escape}" type="text">
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label">Adresse</label>
                  <div class="controls">
                    <input class="span3" name="contactAddress" value="{$team->contactAddress|escape}" type="text">
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label">Postnummer / by</label>
                  <div class="controls">
                    <input class="span3" name="contactPostalCode" value="{$team->contactPostalCode|escape}" type="text">
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label">E-mail</label>
                  <div class="controls">
                    <input class="span3" name="contactMail" value="{$team->contactMail|escape}" type="text">
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label">Telefonnummer</label>
                  <div class="controls">
                    <input class="span3" name="contactPhone" value="{$team->contactPhone|escape}" type="text">
                  </div>
                </div>
                <div class="control-group spejder">
                  <label class="control-label">Rolle ift. patrulje</label>
                  <div class="controls">
                    <input class="span3" name="contactRole" value="{$team->contactRole|escape}" type="text">
                  </div>
                </div>
              </fieldset>
            </div>
          </div> <!-- /span6 -->
          {/if}
          <div class="span6">
            <div class="alert alert-block" style="display:none">
              <fieldset>
                <legend><img src="/assets/icon/glyphicons_283_t-shirt.png"> <span>NYHED: Lækre Nathejk-produkter</span></legend>
                <p style="font-size:14px">Nu kan I købe de helt nye t-shirts og termokopper med Nathejk-logo her i <a href="http://nathejk.spejder.dk/tilmeld/merchandise?<?php print $team->typeName.':'.$team->id; ?>" target="_blank">webshoppen hos DDS Gruppeweb</a> (åbner i nyt vindue). De bestilte varer udleveres på årets Nathejk. I sparer endda 10 procent ved at bestille dem nu i stedet for at købe i salgsboden på Nathejk.</p>
              </fieldset>
            </div>
          </div> <!-- /span6 -->
          <div class="span6">
            <div class="well alert alert-success senior success success-block" style="display:none">
              <fieldset id="team">
                <legend><img src="/assets/icon/glyphicons_239_riflescope.png"> <span>Lokal Område Kontrol</span></legend>
                <div class="control-group">
                  <label class="control-label">Vælg LOK</label>
                  <div class="controls">
                    <select name="lokNumber" id="lok" class="span3">
                      <option>vælg lok</option>
                      {foreach from=$team->allLoks key=lokName item=lok}
                        {if $lok->lokNumber == $team->lokNumber}
                          <option value="{$lok->lokNumber}" selected="selected">{$lok->title|escape}</option>
                        {elseif $team->memberCount + $lok->memberCount <= $lok->maxMemberCount}
                          <option value="{$lok->lokNumber}">{$lok->title|escape}</option>
                        {else}
                          <optgroup label="{$lok->title|escape} (overtegnet)"></optgroup>
                        {/if}
                      {/foreach}
                    </select>
                    <p class="help-block">I kan læse mere om de enkelte LOK's arbejdsopgaver under Nathejk i jeres deltagerbrev.</p>
                  </div>
                </div>
              </fieldset>
            </div> <!-- /well -->
            <div class="well alert alert-success spejder success success-block" style="display:none">
              <fieldset id="team">
                <legend><img src="/assets/icon/glyphicons_014_train.png"> <span>Transport</span></legend>
                <div class="control-group">
                  <label class="control-label">Hvordan ankommer I</label>
                  <div class="controls">
                    <select name="arrivalName" id="arrival" class="span3">
                      <option>vælg ankomst</option>
                      {foreach from=$team->allArrivalTitles key=arrivalName item=arrivalTitle}
                          <option value="{$arrivalName|escape}"{if $arrivalName = $team->arrivalName} selected="selected"{/if}>{$arrivalTitle|escape}</option>
                      {/foreach}
                    </select>
                    <p class="help-block">Hjælp os med at gøre starten hurtigere, angiv hvordan og hvornår i ankommer.</p>
                  </div>
                </div>
              </fieldset>
            </div> <!-- /well -->
          </div> <!-- /span6 -->
        </div>
        <div class="row">
            <div class="span12">
                <p>Deltagerbetalingen bliver ikke refunderet ved afbud uanset grund - vi kan have brugt pengene ud fra en forventning om, at du kommer. Det er dog helt frem til ganske kort før løbsstart muligt at skifte ud blandt deltagerne. Betalingen bliver naturligvis refunderet, hvis holdet ikke deltager, fordi Nathejks team har besluttet det.</p>
          </div> <!-- /span6 -->
        </div>
        <div class="form-actions" style="background:inherit;border:0;border-bottom:1px solid #DDDDDD;margin-top:0;">
          <input class="btn btn-success btn-large" type="button" onclick="$('#teamForm').submit()" value="Fortsæt &raquo;" style="float:right">
        </div>
      </form>

        <div class="row" style="display:none">
          <div class="span4">
            <h3>Køretøj</h3>
            <p>Hvis I medbringer et køretøj skal der udfyldes et registreringsark.</p>
          </div>
          <div class="span4">
            <h3>Mobiltelefonnumre</h3>
            <p>Da der under Nathejk sendes en del sms'er er det vigtigt at jeres mobiltelefonnumre er korrekte.</p>
          </div>
        </div>


<script type="text/javascript">
// validate signup form on keyup and submit
$(document).ready(function(){
    $("#teamForm").validate({
        rules: {
            contactTitle: "required",
            contactAddress: "required",
            contactPostalCode : "required",
            contactMail: {
                required: true,
                email: true
            },
            contactPhone : "required",
            contactRole : "required",
            title : "required",
            gruppe : "required",
        },
        messages: {
            contactTitle: "Skriv navn på kontaktperson",
            contactAddress: "Skal udfyldes",
            contactPostalCode : "Skal udfyldes",
            contactMail: "Skriv kontaktpersons e-mail",
            contactPhone : "Skal udfyldes",
            contactRole : "Skal udfyldes",
            title : "Patruljenavn skal udfyldes",
            gruppe : "Gruppe / division skal angives",
        }
    });
});
$('#team input[name="memberCount"]').change(function() {
    $('#participants tbody tr').show();
    $('#participants tbody tr.member:gt(' + (parseInt(this.value) - 1) + ')').hide();
});
$('#team input[name="memberCount"]:checked').change();

$('.datepicker').datepicker();

</script>

    </div> <!-- /container -->
{/block}
