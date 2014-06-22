<header>
    <h1>Kontrolpunkt</h1>
</header>

<section class="fancyboxContent">
    <form action="" method="post" target="">
        <h1>Oplysninger</h1>

        <fieldset class="formStandard">
            <div class="formTextWrap">
                <label for="f20">Navn:</label>
                <input type="text" class="formText" id="f20" name="title" value="{$team->title|escape}" />
            </div>
            <div class="formTextWrap">
                <label for="f21">Kontrolgruppe:</label>
                <input type="text" class="formText" id="f21" name="gruppe" value="{$team->gruppe|escape}" />
            </div>
            <fieldset class="formStandard Time archiveLocation">
                <div class="formSelectWrap">
                    <label for="t01">Åbningstider:</label>
                    <span>åbner</span>
                    {html_options options=$hours name=startHour id=t01 class=formSelect}
                </div>
                <div class="formSelectWrap">
                    <label for="t02">Time:</label>
                    {html_options options=$minutes name=startMinute id=t02 class=formSelect}
                </div>
                <div class="formSelectWrap">
                    <label for="t01">Time:</label>
                    <span> &nbsp; &nbsp; lukker</span>
                    {html_options options=$hours name=stopHour id=t03 class=formSelect}
                </div>
                <div class="formSelectWrap">
                    <label for="t02">Time:</label>
                    {html_options options=$minutes name=stopMinute id=t04 class=formSelect}
                </div>
            </fieldset>
            
            <div class="formTextareaWrap">
                <label for="f27">Bemærkninger:</label>
                <textarea class="formTextarea" id="f27" name="remark">{$team->remark|escape}</textarea>
            </div>
            <fieldset class="formStandardTime">
                <div class="formSelectWrap">
                    <label for="t01">Registrering:</label>
                    <input type="radio" name="status" value="active"{if $member->status=='active'} checked="checked"{/if}> tjek ind/ud &nbsp;
                </div>
                <div class="formSelectWrap">
                    <label for="t02">Registrering:</label>
                    <input type="radio" name="status" value="paused"{if $member->status=='paused'} checked="checked"{/if}> kun tjek ind &nbsp;
                </div>
            </fieldset>
            <div class="formTextareaWrap">
                <label for="f27">Telefonnumre:</label>
                        {if $team->id}
                        <ul class="fileInfo">
                        {foreach from=$team->members item=member}
                            <li>
                                <button type="submit" name="delete" value="{$member->id|escape}"><span class="icon16x16link">slet</span></button>
                                <input type="text" class="formText" placeholder="telefon" name="member[{$member->id|escape}][phone]" value="{$member->phone|escape}" />
                                <input type="text" class="formText" placeholder="navn" name="member[{$member->id|escape}][title]" value="{$member->title|escape}" />
                            </li>
                        {/foreach}
                            <li>
                                <button type="submit" name="new"><span class="icon16x16link">nyt</span></button>
                            </li>
                            <li style="display:none">
                                <div class="icon16x16link script">
                                    <div class="btn"><a href="upload_01.html" class="fancyboxFullxFull">Add</a></div>
                                    <p>Music Cue Sheet has been created <a href="#">(edit)</a></p>
                                </div>
                            </li>
                        </ul>
                        {/if}
            </div>
        </fieldset>
        <fieldset class="formStandard">
            <div class="formSubmitWrap">
                <input type="submit" value="Gem" class="formSubmit" />
                <span class="formLink"><a href="#" class="cancelLink">Afbryd</a></span>
            </div>
        </fieldset>

    </form>
</section>

