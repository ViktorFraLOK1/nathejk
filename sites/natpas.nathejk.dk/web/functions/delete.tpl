{if isset($redirectUrl)}
    <script type="text/javascript">
        window.parent.location.href = '{$redirectUrl|escape}';
    </script>
{else}
    <header>
        <h1>Slet hold</h1>
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

        <form action="delete.php" method="post" target="">
            <h1>Er du sikker</h1>

            <p>Du er ved at slette:</p>
            <ul>
            {foreach from=$teams item=team}
                <li>{$team->title|escape}</li>
                <input type="hidden" name="ids[]" value="{$team->id|escape}" />
            {/foreach}
            </ul>

            <fieldset class="formStandard">
                <div class="formSubmitWrap">
                    <input type="submit" value="Slet" class="formSubmit" />
                    <span class="formLink"><a href="#" class="cancelLink">Afbryd</a></span>
                </div>
            </fieldset>
        </form>
    {/if}
    </section>
{/if}
