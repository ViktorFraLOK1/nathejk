{if isset($redirectUrl)}
    <script type="text/javascript">
        window.parent.location.href = '{$redirectUrl|escape}';
    </script>
{else}
    <header>
        <h1>Send e-mail</h1>
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
                
        <div class="uploadWrap bulkEdit">

                    <form action="" method="post" target="">
            <section class="containerList">
                <dl id="recipients">
                    <dt>Send mail til:</dt>
                    <dd><input type="checkbox" name="sendToAll" onclick="document.getElementById('recipients').className=this.checked?'all':''"> alle</dd>
                    <dt>Du er ved at sende post til:</dt>
                    {foreach from=$teams item=team}
                        <dd>
                            <a href="{$team->url|escape}" target="_top">{$team->title|escape}<br>
                                <span>{$team->contactTitle|escape} &lt;{$team->contactMail|escape}&gt;</span>
                                {foreach from=$team->members item=member}
                                    {if $member->mail}
                                        <span class="member"><br>{$member->title|escape} &lt;{$member->mail|escape}&gt;</span>
                                    {/if}
                                {/foreach}
                            </a>
                            <input type="hidden" name="ids[]" value="{$team->id|escape}" />
                        </dd>
                    {/foreach}
                </dl>
            </section>

            <section class="containerDetails">

                <div class="containerForm">

                        <fieldset class="formStandard">
                            <h1>Vælg skabelon</h1>
                           <div class="formSelectWrap">
                                <label for="f22">Skabelon:</label>
                                {assign var=mailTemplateId value=0}
                                {if isset($mailTemplate)}
                                    {assign var=mailTemplateId value=$mailTemplate->id}
                                {/if}
                                {html_options id="f22" class="formSelect" options=$mailTemplates name=mailTemplateId selected=$mailTemplateId onchange="this.form.submit()"}
                            </div>
                        </fieldset>

                        <fieldset class="formStandard">
                            <h1>Mail</h1>
                            <div class="formTextWrap">
                                <label for="f15">Emne:</label>
                                <input type="text" class="formText" id="f15" name="subject" value="{if isset($mailTemplate)}{$mailTemplate->subject|escape}{/if}" />
                            </div>
                            <div class="formTextareaWrap">
                                <label for="f27">Indhold:</label>
                                <textarea class="formTextarea" id="f27" name="body">{if isset($mailTemplate)}{$mailTemplate->body|escape}{/if}</textarea>
                            </div>
                        </fieldset>
                        
                        <fieldset class="formStandard">
                            <div class="formSubmitWrap">
                                <input type="submit" value="Send e-mails" name="send" class="formSubmit" />
                            </div>
                            {if !$mailTemplate or $mailTemplate->editable}
                            <div class="formSubmitWrap">
                                <input type="submit" value="Gem som skabelon" name="save" class="formSubmit" />
                            </div>
                            {/if}
                            <div class="formSubmitWrap">
                                <span class="formLink"><a href="#" class="cancelLink">Afbryd</a></span>
                            </div>
                        </fieldset>
                        
                        <fieldset class="batchEdit">
                            <table cellspacing="0" border="0">
                                <thead>
                                    <tr>
                                        <th colspan="2">Variable som automatisk erstattes i udsendte e-mails</h1>
                                    </tr>
                                </thead>
                                <tbody>
                                    {foreach from=$variableDescriptions key=variableName item=variableDescription}
                                    <tr>
                                        <td style="width:180px;vertical-align:top;">#{$variableName|strtoupper|escape}#</td>
                                        <td class="container" style="width:auto!important;"><span>{$variableDescription|escape}</span></td>
                                    </tr>
                                    {/foreach}
                                </tbody>
                            </table>
                        </fieldset>

                </div>
            </section>
                    </form>
        </div>
    {/if}
    </section>
{/if}
