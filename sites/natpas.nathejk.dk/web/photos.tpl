<div class="savedSearchesWrap singleColumn">
    <div class="column">
    <section>
        <h1>Unavngivne billeder</h1>

        <form action="" method="post">
            <fieldset>
                <ul class="image">
                {foreach from=$photos item=photo}
                        <li>
                            <div>
                                <a class="fancybox640x480" href="{$photo->url|escape}"><img src="{$photo->url|escape}"></a>
                                <input type="text" placeholder="nr." name="photo[{$photo->id|escape}]" value="">
                                <span style="color:999; font-style:italic;">x = slet</span>
                                <p style="display:none;">kl. {$photo->createUts|date_format:'%R'}</p>
                            </div>
                        </li>
                {/foreach}
                </ul>
            </fieldset>

            <fieldset class="formStandard">
                <div class="formSubmitWrap">
                    <input type="submit" value="Gem" class="formSubmit" />
                    <input type="submit" value="Importer" name="scanDir" class="formSubmit" />
                </div>
            </fieldset>
        </form>

    </section>
    </div>
</div>
                
