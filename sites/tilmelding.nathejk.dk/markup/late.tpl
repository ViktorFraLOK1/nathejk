{extends file="page.tpl"}
{block name=titleText}{$agenda->title|escape}{/block}
{block name=container}
    <div class="container">
      <div class="page-header">
        <h1>Tilmelding <small>overvældende interesse</small></h1>
      </div>
      <div class="row">
        <div class="span12">
          <h3>I er kommet på venteliste</h3>
          <p>Der er desværre allerede for mange tilmeldte på {$agenda->title|escape}. Jeres tilmelding kom for sent i forhold til de hurtigste og derfor er vi nødt til i første omgang at placere jer på ventelisten. I får besked, så hurtigt som muligt, hvis det viser sig, at der bliver plads til jer.</p>

          <p>Vi sætter stor pris på jeres interesse og håber, at vi ses på årets løb!</p>

          <p>Mange hilsner fra Nathejk</p>
        </div>
      </div>

    </div> <!-- /container -->
{/block}

