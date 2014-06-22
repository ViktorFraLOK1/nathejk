<header>
    <h1>Deltager</h1>
</header>

<section class="fancyboxContent">
    <form action="" method="post" target="">
        <h1>Personoplysninger</h1>

        <fieldset class="formStandard">
            <div class="formTextWrap">
                <label for="f20">Navn:</label>
                <input type="text" class="formText" id="f20" name="title" value="{$member->title|escape}" />
            </div>
            <div class="formTextWrap">
                <label for="f21">Adresse:</label>
                <input type="text" class="formText" id="f21" name="address" value="{$member->address|escape}" />
            </div>
            <div class="formTextWrap">
                <label for="f22">Postnummer:</label>
                <input type="text" class="formText" id="f22" name="postalCode" value="{$member->postalCode|escape}" />
            </div>
            <div class="formTextWrap">
                <label for="f25">Eget telefonr:</label>
                <input type="text" class="formText" id="f25" name="phone" value="{$member->phone|escape}" />
            </div>
            {if $member->team->memberClassName == 'Nathejk_Spejder'}
            <div class="formTextWrap">
                <label for="f251">Kontaktperson tlf.:</label>
                <input type="text" class="formText" id="f251" name="contactPhone" value="{$member->contactPhone|escape}" />
            </div>
            <div class="formTextWrap">
                <label for="f251w">Kontaktperson bekræftet:</label>
                <input type="text" class="formText" id="f251w" name="contactSms" disabled="disabled" value="{$member->contactSms|escape}" />
            </div>
            {/if}
            <div class="formTextWrap">
                <label for="f24">E-mail-adresse:</label>
                <input type="text" class="formText" id="f24" name="mail" value="{$member->mail|escape}" />
            </div>
            <div class="formTextWrap"> 
                <label for="dateaf25">Fødselsdag:</label> 
                <input type="text" class="formText datePicker" id="dateaf25" name="birthDate" value="{$member->birthDate|date_format:'%d/%m/%Y'}" /> 
            </div>
            <div class="formCheckboxWrap">
                <label for="f23">Har været med før:</label>
                <input type="checkbox" class="formCheckbox" id="f23" name="returning"{if $member->returning} checked="checked"{/if} />
            </div>
            <div class="formTextareaWrap">
                <label for="f27">Bemærkninger:</label>
                <textarea class="formTextarea" id="f27" name="remark">{$member->remark|escape}</textarea>
            </div>{*
                                    <fieldset class="formStandardMultiple">
                                        <h2 class="pseudoLabel">Received</h2>
                                        <div class="formTextWrap">
                                            <label for="receivedfrom">- From:</label>
                                            <input type="text" class="formText" id="receivedfrom" />
                                        </div>
                                        <div class="formTextWrap">
                                            <label for="receivedDate">- Date:</label>
                                            <input type="text" class="formText datePicker" id="receivedDate" />
                                        </div>
                                    </fieldset>
                                    *}
            <fieldset class="formStandardTime">
                <div class="formSelectWrap">
                    <label for="t01">Status:</label>
                    <input type="radio" name="status" value="active"{if $member->status=='active'} checked="checked"{/if}> aktiv &nbsp;
                </div>
                <div class="formSelectWrap">
                    <label for="t02">Status:</label>
                    <input type="radio" name="status" value="paused"{if $member->status=='paused'} checked="checked"{/if}> udgået &nbsp;
                </div>
                <div class="formSelectWrap">
                    <label for="t03">Status:</label>
                    <input type="radio" name="status" value="discontinued"{if $member->status=='discontinued'} checked="checked"{/if}> afhentet &nbsp;
                </div>
            </fieldset>
        </fieldset>

        <fieldset class="formStandard">
            <div class="formSubmitWrap">
                <input type="submit" value="Gem" class="formSubmit" />
                <span class="formLink"><a href="#" class="cancelLink">Afbryd</a></span>
            </div>
        </fieldset>

    </form>
</section>

