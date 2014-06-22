<header>
    <h1>Fangst - check ind/ud</h1>
</header>

<section class="fancyboxContent">
    <form action="" method="post" target="">
        <h1>Ret</h1>

        <fieldset class="formStandard">
            <div class="formTextWrap">
                <label for="f20">Check ind:</label>
                <input type="text" class="formText" id="f20" name="inDateTime" value="{$checkIn->createdUts|date_format:'%Y-%m-%d %R'|escape}" />
            </div>
            <div class="formTextWrap">
                <label for="f21">Check ud:</label>
                <input type="text" class="formText" id="f21" name="outDateTime" value="{if $checkIn->outUts}{$checkIn->outUts|date_format:'%Y-%m-%d %R'|escape}{/if}" />
            </div>
            <div class="formTextWrap">
                <label for="f22">Patrulje id (ikke nummer):</label>
                <input type="text" class="formText" id="f22" name="teamId" value="{$checkIn->teamId|escape}" />
            </div>
            <div class="formTextWrap">
                <label for="f24">Senior id (ikke nummer):</label>
                <input type="text" class="formText" id="f24" name="memberId" value="{$checkIn->memberId|escape}" />
            </div>
            <div class="formTextWrap">
                <label for="f22">Koordinat:</label>
                <input type="text" class="formText" id="f22" name="location" value="{$checkIn->location|escape}" />
            </div>
            <div class="formTextWrap">
                <label for="f24">Bemærkning:</label>
                <input type="text" class="formText" id="f24" name="remark" value="{$checkIn->remark|escape}" />
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

