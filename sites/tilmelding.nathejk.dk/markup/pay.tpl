{extends file="page.tpl"}
{block name=titleText}{$agenda->title|escape}{/block}
{block name=container}
    <div class="container">
      <div class="page-header">
        <h1>Tilmelding <small>betaling via DDS Gruppeweb</small></h1>
      </div>
      <div class="row">
        <div class="span12">
            {if $team->typeName == 'patrulje'}
                {assign var=offset value=90}
            {else}
                {assign var=offset value=97}
            {/if}
            <iframe style="width:100%;height:500px" src="http://nathejk.spejder.dk/tilmeld/2013?emptycart=true&edit[attributes][1]={$team->title|urlencode}%20[{$team->id|intval}]&edit[attributes][26]={$team->unpaidMemberCount|intval+$offset}"></iframe>
        </div>
      </div>

    </div> <!-- /container -->
{/block}

