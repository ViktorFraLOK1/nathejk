

<div class="productWrap">
    
    <section class="productList">
        <h1>Tilmeldte ({$team->activeMembers|@count} personer)</h1>
        <table border="0" cellspacing="0" class="products">
            <tbody>
            {foreach from=$team->activeMembers item=member}
                <tr>
                    <td class="distId"><span>{if $member->number}{$member->number|escape}{else}&times;&times;&times;{/if}</span></td>
                    <td><a href="member.php?id={$member->id|escape}" class="fancybox600x520">{$member->title|escape|default:'<em>- intet -</em>'}{if $member->remark}<span style="margin-left:4px;padding-left:16px" class="icon16x16remark" title="{$member->remark|escape}"></span>{/if}</a></td>
                </tr>
            {/foreach}
            </tbody>
        </table>

        <ul class="actionbar">
        <li><a class="fancybox600x520 icon16x16user-add confirm" href="member.php?teamId={$team->id|escape}">tilføj</a></li>
        <li><a class="fancyboxFullxFull icon16x16group-edit confirm" href="members.php?teamId={$team->id|escape}">ret alle</a></li>
        {if $team->typeName == 'patrulje'}<li><a class="fancybox600x480 icon16x16group-link" href="join.php?teamId={$team->id|escape}">sammenlæg</a></li>{/if}
        </ul>

        {foreach from=$team->teams item=subteam}
            <h1><a href="team.php?id={$subteam->id|escape}">{$subteam->title|escape}</a></h1>
            <table border="0" cellspacing="0" class="products">
                <tbody>
                    {foreach from=$subteam->activeMembers item=member}
                        <tr>
                            <td class="distId"><span>{if $member->number}{$member->number|escape}{else}&times;&times;&times;{/if}</span></td>
                            <td><a href="member.php?id={$member->id|escape}" class="fancybox600x480">{$member->title|escape}{if $member->remark}<span style="margin-left:4px;padding-left:16px" class="icon16x16remark" title="{$member->remark|escape}"></span>{/if}</a></td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        {/foreach}
       
        {if $team->pausedMembers}
        <h1>Udg&aring;ede ({$team->pausedMembers|@count} personer)</h1>
        <table border="0" cellspacing="0" class="products">
            <tbody>
            {foreach from=$team->pausedMembers item=member}
                <tr>
                    <td>{$member->pausedUts|date_format:'%a. %R'}</td>
                    <td><a href="member.php?id={$member->id|escape}" class="fancybox600x480">{$member->title|escape}{if $member->remark}<span style="margin-left:4px;padding-left:16px" class="icon16x16remark" title="{$member->remark|escape}"></span>{/if}</a></td>
                </tr>
            {/foreach}
            </tbody>
        </table>
        {/if}
       
        {if $team->discontinuedMembers}
        <h1>Afhentede ({$team->discontinuedMembers|@count} personer)</h1>
        <table border="0" cellspacing="0" class="products">
            <tbody>
            {foreach from=$team->discontinuedMembers item=member}
                <tr>
                    <td>{$member->discontinuedUts|date_format:'%a. %R'}</td>
                    <td><a href="member.php?id={$member->id|escape}" class="fancybox600x480">{$member->title|escape}{if $member->remark}<span style="margin-left:4px;padding-left:16px" class="icon16x16remark" title="{$member->remark|escape}"></span>{/if}</a></td>
                </tr>
            {/foreach}
            </tbody>
        </table>
        {/if}
       
        {* if $team->deletedMembers}
        <h1>Slettede ({$team->deletedMembers|@count} personer)</a></h1>
        <table border="0" cellspacing="0" class="products">
            <tbody>
            {foreach from=$team->deletedMembers item=member}
                <tr>
                    <td>{$member->deletedUts|date_format:'%d/%m kl. %H:%M'}</td>
                    <td><a href="member.php?id={$member->id|escape}" class="fancybox600x480">{$member->title|escape}</a></td>
                </tr>
            {/foreach}
            </tbody>
        </table>
        {/if *}

        {if $team->teamNumber}
        <img src="/qr.image.php?teamId={$team->id|escape}" alt="" style="margin:10px 20px 0px;">
        {/if}            
    </section>
    
    <section class="masterProductContainer">
        
        <hgroup>
            {if $team->typeName == 'klan'}
            <img src="senior32x32.png" style="float:left;padding:10px;">
            {elseif $team->typeName == 'patrulje'}
            <img src="spejder32x32.png" style="float:left;padding:10px;">
            {/if}
            <h1>{$team->title|escape|default:"&nbsp;"}</h1>
            <h2>{$team->gruppe|escape|default:"&nbsp;"}</h2>
        </hgroup>
                                
        <ul class="prodDetails">
            <li>
                <div class="{if $team->typeName=='patrulje'}icon16x16group{else}icon16x16denmark{/if}">
                    <table border="0" cellspacing="0">
                        <tr>
                            <td><span>Hold ID</span></td>
                            <td><a href="{$team->frontendUrl|escape}">{$team->id|escape}</a></td>
                        </tr>
                        {if $team->teamNumber}
                        <tr>
                            <td><span>Patruljenummer</span></td>
                            <td>{$team->teamNumber|escape}</td>
                        </tr>
                        <tr>
                            <td><span>Downloads</span></td>
                            <td>
                                <a style="background-repeat:no-repeat;padding-left:20px" class="icon16x16vcard" href="functions/export.natpas.pdf.php?ids={$team->id|escape}">Natpas</a>,
                            </td>
                        </tr>
                        {/if}
                        <tr>
                            <td><span>Tilmelding startet</span></td>
                            <td>{$team->createdUts|date_format:'%e. %B kl. %H:%M:%S'|strtolower}</td>
                        </tr>
                        <tr>
                            <td><span>Tilmelding afsluttet</span></td>
                            <td>{if $team->openedUts}{$team->openedUts|date_format:'%e. %B kl. %H:%M:%S'|strtolower}{else}-{/if}</td>
                        </tr>
                        <tr>
                            <td><span>Tilmelding sidst opdateret</span></td>
                            <td>{if $team->lastModifyUts}{$team->lastModifyUts|date_format:'%e. %B kl. %H:%M:%S'|strtolower}{else}-{/if}</td>
                        </tr>
                        <tr>
                            <td><span>Antal fangster</span></td>
                            <td><a href="capture.php?teamId={$team->id|escape}">{$team->catchCount|escape}</a></td>
                        </tr>
                    </table>
                </div>
            </li>
        </ul>
        
{*        <form action="" method="post">
            
            <fieldset class="formStandard">
                <div class="column">
                    <h1>Fangster/check-ins</h1>
                    <div class="formTextWrap"> 
                        <label for="dateaf25">Tidspunkt:</label> 
                        <input type="text" class="formText" id="dateaf25" name="checkInDateTime" value="{$smarty.now|date_format:'%Y-%m-%d %R'}" /> 
                    </div>
                    <div class="formTextWrap"> 
                        <label for="f25">Position:</label> 
                        <input type="text" class="formText" id="f25" name="checkInPosition" /> 
                    </div>
                    <div class="formTextareaWrap">
                        <label for="f27">Bemærkninger:</label>
                        <textarea class="formTextarea" id="f27" name="checkInRemark"></textarea>
                    </div>
                </div>
            </fieldset>

            <fieldset class="formStandard">
                <div class="formSubmitWrap">
                    <input type="submit" name="checkIn" value="Gem bemærkning" class="formSubmit" />
                </div>
            </fieldset>
            <fieldset class="formStandard">
            {foreach from=$team->checkIns item=checkIn}
                <div class="formTextWrapDisabled">
                    <span class="pseudoLabel">{$checkIn->createdUts|date_format:'%a.  %R'}{if $checkIn->outUts} - {$checkIn->outUts|date_format:'%R'}{/if}:</span>
                    <span class="pseudoFormText">{$checkIn->remark|escape}</span>
                </div>
            {/foreach}
            </fieldset>
                                        
        </form>
*}
        <form action="" method="post">
            
            <fieldset class="formStandard">
                
                <div class="column iblock">
                    <h1>{if $team->typeName == 'patrulje'}Patruljeoplysninger{else}Klanoplysninger{/if}</h1>
                    <div class="formCheckboxWrap">
                        <label for="f33">Kontrolleret ved start:</label>
                        <input type="checkbox" class="formCheckbox" id="f33" name="checkedAtStart"{if $team->checkedAtStart} checked="checked"{/if} />
                    </div>
                    <div class="formTextWrap">
                        <label for="f11">{if $team->typeName == 'patrulje'}Patruljenavn{else}Klannavn{/if}:</label>
                        <input type="text" name="title" class="formText" id="f11" value="{$team->title|escape}" />
                    </div>
                    <div class="formTextWrap">
                        <label for="f12">Gruppe og division:</label>
                        <input type="text" name="gruppe" class="formText" id="f12" value="{$team->gruppe|escape}" />
                    </div>
                    <div class="formSelectWrap">
                        <label for="f22">Korps:</label>
                        {html_options id="f22" class="formSelect" name="korps" options=$team->allKorps selected=$team->korps}
                    </div>
                    {if $team->typeName != 'patrulje'}
                    <div class="formSelectWrap">
                        <label for="f23">Ønskede antal seniorer:</label>
                        {html_options id="f23" class="formSelect" name="memberCount" options=$seniorCountOptions selected=$team->memberCount}
                    </div>
                    {else}
                    <div class="formTextWrap">
                        <label for="f13">Adventurespejdliganummer:</label>
                        <input type="text" name="ligaNumber" class="formText" id="f13" value="{$team->ligaNumber|escape}" />
                    </div>
                    <div class="formTextareaWrap">
                        <label for="f27">Bemærkninger:</label>
                        <textarea class="formTextarea" id="f27" name="remark">{$team->remark|escape}</textarea>
                    </div>
                    {/if}
                </div>
                
                <div class="column iblock">
                    <h1>Kontaktperson</h1>
                    <div class="formTextWrap">
                        <label for="g11">Navn:</label>
                        <input type="text" class="formText" id="g11" name="contactTitle" value="{$team->contactTitle|escape}" />
                    </div>
                    <div class="formTextWrap">
                        <label for="g110">Adresse:</label>
                        <input type="text" class="formText" id="g110" name="contactAddress" value="{$team->contactAddress|escape}" />
                    </div>
                    <div class="formTextWrap">
                        <label for="g111">Postnummer:</label>
                        <input type="text" class="formText" id="g111" name="contactPostalCode" value="{$team->contactPostalCode|escape}" />
                    </div>
                    <div class="formTextWrap">
                        <label for="g12">E-mail-adresse:</label>
                        <input type="text" class="formText" id="g12" name="contactMail" value="{$team->contactMail|escape}" />
                    </div>
                    <div class="formTextWrap">
                        <label for="g13">Telefonnummer:</label>
                        <input type="text" class="formText" id="g13" name="contactPhone" value="{$team->contactPhone|escape}" />
                    </div>
                    {if $team->typeName == 'patrulje'}
                    <div class="formTextWrap">
                        <label for="g14">Rolle i forhold til patruljen:</label>
                        <input type="text" class="formText" id="g14" name="contactRole" value="{$team->contactRole|escape}" />
                    </div>
                    {/if}
                </div>


                {if $team->typeName != 'patrulje'}
                <div class="column">
                    <h1>Placering</h1>
                    <div class="formSelectWrap">
                        <label for="f43">Tilmeldingsstatus:</label>
                        {html_options id="f43" class="formSelect" name="signupStatusTypeName" options=$team->allSignupStatusTypes selected=$team->signupStatusTypeName}
                    </div>
                    <div class="formSelectWrap">
                        <label for="f44">Placering på Nathejk:</label>
                        {html_options id="f44" class="formSelect" name="lokNumber" options=$lokNumbers selected=$team->lokNumber}
                    </div>
                    <div class="formTextareaWrap">
                        <label for="f27">Bemærkninger:</label>
                        <textarea class="formTextarea" id="f27" name="remark">{$team->remark|escape}</textarea>
                    </div>
                </div>
                {/if}

            </fieldset>
            
            <fieldset class="formStandard">
                <div class="formSubmitWrap">
                    <input type="submit" value="Save" class="formSubmit" />
                </div>
            </fieldset>
                                        
            {if $team->payments}
            <div class="column">
                <h1>Indbetalinger af dette hold</h1>
                <dl class="historical">
                {foreach from=$team->payments item=payment}
                    <dt>{$payment->uts|date_format:'%Y-%m-%d'}</dt>
                    <dd>{$payment->amount|escape},-</dd>
                {/foreach}
                </dl>
            </div>
            {/if}

            {if $team->mails}
            <div class="column">
                <h1>E-mails sendt til dette hold</h1>
                <dl class="historical">
                {foreach from=$team->mails item=mail}
                    <dt>{$mail->sendUts|date_format:'%Y-%m-%d kl. %H:%M'}</dt>
                    <dd>{if $mail->smtpErrorMessage}<i class="icon-exclamation-sign" style="color:red" title="{$mail->smtpErrorMessage|escape}"></i> {/if}<a href="displaymail.php?id={$mail->id|escape}" class="fancybox600x480">{$mail->subject|escape}</a></dd>
                {/foreach}
                </dl>
            </div>
            {/if}
        
        </form>
    </section>
    
</div>
