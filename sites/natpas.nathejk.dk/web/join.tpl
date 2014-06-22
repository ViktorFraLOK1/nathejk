<header>
    <h1>Sammenlæg hold</h1>
</header>

<section class="fancyboxContent">

{if isset($message)}
    <div class="systemMessage">
        <p>{$message}</p>
    </div>
    <fieldset class="formStandard">
        <div class="formSubmitWrap">
            <span class="formLink"><a href="#" class="cancelLink">Luk</a></span>
        </div>
    </fieldset>
{else}

    <p style="font-size:16px;">Du er ved at slå følgende to hold sammen.</p>
    <p style="font-size:16px;"><b>{if $team->teamNumber}{$team->teamNumber|escape}, {/if}{$team->title|escape}, {$team->gruppe|escape}</b> vil efterfølgende være en del af:</p>

    <form action="" method="post" target="">
        <fieldset class="formStandard">
            <div class="formSelectWrap">
                <label for="teamId">Hold:</label>
                <select name="teamId">
                {foreach from=$teams item=t}
                    <option value="{$t->id|escape}"{if $t->id == $team->parentTeamId} selected="selected"{/if}>{if $t->teamNumber}{$t->teamNumber|escape}, {/if}{$t->title|escape}</option>
                {/foreach}
                </select>
            </div>
        </fieldset>

        <fieldset class="formStandard">
            <div class="formSubmitWrap">
                <input type="submit" value="Sammenlæg" class="formSubmit" />
                <span class="formLink"><a href="#" class="cancelLink">Afbryd</a></span>
            </div>
        </fieldset>
    </form>
{/if}
</section>

