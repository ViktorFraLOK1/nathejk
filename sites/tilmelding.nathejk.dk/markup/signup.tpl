{extends file="page.tpl"}
{block name=titleText}{$agenda->title|escape}{/block}
{block name=container}

{function name=signupForm teamTypeName="none" colorSchemeName="primary"}
<form class="form-horizontal signup" style="margin:0;" action="" method="post">
    <fieldset>
        <input type="hidden" name="teamTypeName" value="{$teamTypeName|escape}" />
        <p>Indtast først dine (tilmelderens) kontaktoplysninger</p>
        <hr style="border:0; border-top:1px solid #DDDDDD;" />
        <div class="control-group">
            <label class="control-label">Dit navn</label>
            <div class="controls">
                <div class="input-prepend">
                    <span class="add-on"><i class="icon-user"></i></span><input class="span2" name="contactName" placeholder="navn" size="16" type="text">
                </div>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">Dit mobilnummer</label>
            <div class="controls">
                <div class="input-prepend">
                    <span class="add-on"><i class="icon-signal"></i></span><input class="span2" name="contactPhone" placeholder="nummer" size="16" type="text">
                </div>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label">Din e-mail</label>
            <div class="controls">
                <div class="input-prepend">
                    <span class="add-on"><i class="icon-envelope"></i></span><input class="span2" name="contactMail" placeholder="adresse" size="16" type="text">
                </div>
            </div>
        </div>
        <div class="form-actions" style="margin-bottom:0;padding-bottom:0;">
            <button type="submit" class="btn btn-{$colorSchemeName|escape}">Fortsæt</button>
            <button type="button" class="btn" onclick="$(this).closest('.stepManager').removeClass('show-step2').addClass('show-step1')">Fortryd</button>
        </div>
    </fieldset>
</form>
{/function}

    <div class="container">
      <div class="page-header">
        <h1>{$agenda->title|escape} <small>20. - 22. september - et sted på Sjælland.</small></h1>
      </div>
      <div class="row">
        <div class="span6">
        <div class="hero-unit alert alert-info" style="min-height:530px;">
            {if !$agenda->signupSpejderOpen}<span class="label label-warning pull-right">Tilmeldingen er lukket</span>{/if}
          <h1>Spejdere <small>12-17 år</small></h1>
          <div id="spejder-step" class="stepManager show-step1">
            <div class="step1">
            
            <p>På jagt efter kemiske våben:  Situationen i Natland giver os anledning til stigende bekymring! Som FN-inspektører skal I undersøge de vedholdende rygter om, at styret i Natland udvikler kemiske våben og overtræder menneskerettighederne. I kan rejse ind i landet som kong Mundo af Natlands officielle gæster, men tag endelig ikke fejl: Mundos sikkerhedsstyrker er kendt for at få indsamlede beviser til at “forsvinde” – og de seneste rapporter melder om, at styret nu også får støtte fra internationalt efterlyste krigsforbrydere... så vær skarpe, vær hurtige og vær på vagt!</p>
            <p>Regler for patruljer:</p>
            <ol>
            <li>Der skal mindst være minimum 3 deltagere på holdet - og max 7</li>
            <li>Ingen er under 12 år.</li>
            <li>Ingen er fyldt 18 år.</li>
            <li>Holdets gennemsnitsalder skal være mindst 13 år.</li>
            </ol>
            <p>
                {if $agenda->signupSpejderOpen}
                    <a class="btn btn-primary btn-large" onclick="$('#spejder-step').removeClass('show-step1').addClass('show-step2')">Tilmeld patrulje &raquo;</a>
                {else}
                    <a class="btn btn-primary btn-large disabled">Tilmeld patrulje &raquo;</a>
                {/if}
            </p>
            </div>
          <div class="step2">
            {signupForm teamTypeName=patrulje}
          </div>
          </div>
        </div>
        </div>

        <div class="span6">
        <div class="hero-unit alert alert-error" style="min-height:530px;">
            {if !$agenda->signupSeniorOpen}<span class="label label-warning pull-right">Tilmeldingen er lukket</span>{/if}
          <h1>Seniorer <small>+15 år</small></h1>
          <div id="senior-step" class="stepManager show-step1">
          <div class="step1">
            <p>Stop spionerne! Fortrolig besked til Natlands sikkerhedsstyrker: De vestlige kujoner kræver at sende “inspektører” ind i Natland, hvis vi vil undgå en invasion. Jeg har accepteret deres naive krav, så vi kan nå at færdiggøre de projekter, der endeligt kan udslette vores fjender. Vestens spioner (de såkaldte inspektører) er på vej nu - og jeg forventer, at I gør alt for, at Natlands fabrikker fortsat får total arbejdsro. Den endelige sejr afhænger af jer!</p>
            <p>Kong Mundo</p>
            <p>
                {if $agenda->signupSeniorOpen}
                    <a class="btn btn-danger btn-large" onclick="$('#senior-step').removeClass('show-step1').addClass('show-step2')">Tilmeld klan &raquo;</a>
                {else}
                    <a class="btn btn-danger btn-large disabled">Tilmeld klan &raquo;</a>
                {/if}
            </p>
          </div>
          <div class="step2">
            {signupForm teamTypeName="klan" colorSchemeName="danger"}
          </div>
          </div>
        </div>
        </div>

    </div><!--/.row -->
      <div class="row">
        <div class="span4">
          <h2 class="with-icon"><img src="/assets/icon/glyphicons_060_compass.png">Hvad</h2>
          <p>Nathejk er en blanding af hejk og natløb og varer cirka 36 timer. Tropsspejderne går 35-50 km og skal smugle én eller flere genstande undervejs uden at blive fanget af banditterne (seniorerne), der patruljerer i løbsområdet og som fanger patruljerne og gennemsøger deres oppakning for smuglervarer.</p>
        </div>
        <div class="span4">
          <h2 class="with-icon"><img src="/assets/icon/icon_denmark.png">Hvor</h2>
          <p>Nathejk er et løb for tropsspejdere i alderen 12-17 år og for seniorer. Løbet begynder altid den 3. fredag i september et sted på Sjælland. Startstedet afsløres cirka en måned før løbet.</p>
        </div>
        <div class="span4">
          <h2 class="with-icon"><img src="/assets/icon/glyphicons_332_certificate.png">Hvorfor</h2>
          <p>Det begyndte i FNs flygtningeår i 1986, hvor en trop ville have sine spejdere ud at mærke flygtningenes problemer på deres egne kroppe. De skulle – i ly af mørket – vandre frem til mål, selv sørge for bespisning undervejs og finde et sted at overnatte, mens de blev eftersøgt af “banditter” (seniorer). Ideen fængede og siden er arrangementet støt og roligt vokset.</p>
        </div>
      </div>

<script type="text/javascript">
// validate signup form on keyup and submit
$(document).ready(function(){
    $(".signup").validate({
        rules: {
            contactName: "required",
            contactMail: {
                required: true,
                email: true
            },
        },
        messages: {
            contactName: "Skriv dit navn",
            contactMail: "Skriv din e-mailadresse",
        }
    });
});
</script>

    </div> <!-- /container -->
{/block}
