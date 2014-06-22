{extends file="scan/page.tpl"}
{block name=container}
      <div class="starter-template">
        <h1>{$team->teamNumber}-{$team->memberCount}. {$team->title|escape}</h1>
        {if $member->isBandit()}
            {if $team->catchCount == 1}
                <p class="lead">BINGO, det er først gang at denne patrulje bliver fanget</p>
            {else}
                <p class="lead">Denne patrulje er nu blevet fanget {$team->catchCount} gange.</p>
            {/if}
        {else}
            <p class="lead">Denne patrulje er blevet fanget {$team->catchCount} gange - i alt er de nu blevet scannet {$team->contactCount} gange</p>
        {/if}
        {if $team->noticeText}<p class="lead" style="color:red">{$team->noticeText|escape}</p>{/if}
      </div>
{/block}
