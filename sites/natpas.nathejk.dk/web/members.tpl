<header>
    <h1>Medlemmer</h1>
</header>

<section class="fancyboxContent">
    <div class="uploadWrap">
        
        <section class="containerDetails">
            <div class="containerForm">
            
                <form action="#" method="post">
                    <input type="hidden" name="formGenerateUts" value="{$smarty.now}" />
                                                    
                    <fieldset class="batchEdit">
                        <table cellspacing="0" border="0">
                            <thead>
                                <tr>
                                    {if !$team->teamNumber}<th class="duration" style="width:30px">Nr.</th>{/if}
                                    <th class="container">Deltagernavn</th>
                                    <th class="dist-episode">Adresse</th>
                                    <th class="duration" style="width:70px">Postnr.</th>
                                    {if !$team->teamNumber}
                                    <th class="mctitle" style="width:70px">Telefon</th>
                                    <th class="duration">E-mail</th>
                                    {else}
                                    <th class="mctitle" style="width:70px">Egen tlf.</th>
                                    <th class="duration">Kontakttelefonnumre</th>
                                    {/if}
                                </tr>
                            </thead>
                            <tbody>
                            {foreach from=$team->members item=member}
                                <tr>
                                    {if !$team->teamNumber}
                                    <td style="width:auto">
                                        <div class="formTextWrap">
                                            <label>Nummer</label>
                                            <input type="text" class="formText" name="member[{$member->id}][number]" value="{$member->number|escape}" />
                                        </div>
                                    </td>
                                    {/if}
                                    <td style="width:auto">{$member->title|escape}</td>
                                    <td style="width:auto">
                                        <div class="formTextWrap">
                                            <label>Adresse</label>
                                            <input type="text" class="formText" name="member[{$member->id}][address]" value="{$member->address|escape}" />
                                        </div>
                                    </td>
                                    <td style="width:auto">
                                        <div class="formTextWrap">
                                            <label>Postnr.</label>
                                            <input type="text" class="formText" name="member[{$member->id}][postalCode]" value="{$member->postalCode|escape}" />
                                        </div>
                                    </td>
                                    <td style="width:auto">
                                        <div class="formTextWrap">
                                            <label>Egen tlf.</label>
                                            <input type="text" class="formText" name="member[{$member->id}][phone]" value="{$member->phone|escape}" />
                                        </div>
                                    </td>
                                    <td style="width:auto">
                                        <div class="formTextWrap">
                                        {if $team->teamNumber}
                                            <label>Kontakttelefonnumre</label>
                                            <input type="text" class="formText" name="member[{$member->id}][contactPhone]" value="{$member->contactPhone|escape}" />
                                        {else}
                                            <label>E-mail</label>
                                            <input type="text" class="formText" name="member[{$member->id}][mail]" value="{$member->mail|escape}" />
                                        {/if}
                                        </div>
                                    </td>
                                </tr>
                            {/foreach}
                            </tbody>
                        </table>
                        
                    </fieldset>
                    
                    <fieldset class="formStandard">
                        <div class="formSubmitWrap">
                            <input type="submit" value="Gem" class="formSubmit" />
                            <span class="formLink"><a href="#" class="cancelLink">Afbryd</a></span>
                        </div>
                    </fieldset>
                </form>

            </div>
        </section>
        
    </div>
</section>

