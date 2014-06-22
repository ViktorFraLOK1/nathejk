{if isset($redirectUrl)}
    <script type="text/javascript">
        window.parent.location.href = '{$redirectUrl|escape}';
    </script>
{else}
    <header>
        <h1>Eksporter</h1>
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

        <form action="" method="post" target="">
            <h1>Vælg format</h1>
            <p>
            <button type="submit" name="exportFormat" value="excel">Excel 95</button>
            <button type="submit" name="exportFormat" value="pdf">Stamkort (pdf)</button>
            <button type="submit" name="exportFormat" value="inout">Afkrydsningsliste</button>
            <button type="submit" name="exportFormat" value="natpas">Natpas</button>
            </p>
            <fieldset class="formStandard">
                <div class="formSubmitWrap">
                    <span class="formLink"><a href="#" class="cancelLink">Afbryd</a></span>
                </div>
            </fieldset>
        </form>
    {/if}
    </section>
{/if}
