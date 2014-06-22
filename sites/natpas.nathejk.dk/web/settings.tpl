<form action="" method="post" class="settingsModules">
                    
    <section class="mainSearchBasic">
        <h1>Nedtælling</h1>
        <fieldset>
            <div class="column">
                <div class="formTextWrap">
                    <label for="f06">Nedtællingstidspunkt</label>
                    <input type="text" class="formText" id="f06" name="signupStartDateTime" value="{$agenda->signupStartUts|date_format:'%Y-%m-%d %H:%M:%S'}" />
                </div>
                <div class="formTextareaWrap">
                    <label for="f07">Spejderintro:</label>
                    <textarea class="formTextarea" id="f07" name="spejderIntro">{$agenda->spejderIntro|escape}</textarea>
                    <br clear="both">
                </div>
                <div class="formTextareaWrap">
                    <label for="f08">Seniorintro:</label>
                    <textarea class="formTextarea" id="f08" name="seniorIntro">{$agenda->seniorIntro|escape}</textarea>
                </div>
            </div>
        </fieldset>
    </section>
    <section>
        <h1>Tilmelding</h1>
        <fieldset>
            <div class="column">
                <div class="formCheckboxWrap">
                    <input type="checkbox" id="something08" class="formCheckbox" name="signupSpejderOpen"{if $agenda->signupSpejderOpen} checked="checked"{/if} />
                    <label for="something08">Åben for spejdertilmelding</label>
                </div>
                <div class="formCheckboxWrap">
                    <input type="checkbox" id="something09" class="formCheckbox" name="signupSeniorOpen"{if $agenda->signupSeniorOpen} checked="checked"{/if} />
                    <label for="something09">Åben for seniortilmelding</label>
                    {if !$agenda->signupSeniorOpen}
                        <span class="badge badge-red">lukket</span>
                    {elseif !$agenda->isOpenForSeniorSignup()}
                        <span class="badge badge-orange">venteliste</span>
                    {else}
                        <span class="badge badge-green">åben</span>
                    {/if}
                        
                </div>
                <div class="formTextWrap">
                    <label for="f05">Maks. antal seniorer</label>
                    <input type="text" class="formText" id="f05" name="maxSeniorMemberCount" value="{$agenda->maxSeniorMemberCount|escape}" />
                </div>
            </div>
        </fieldset>
    </section>
{*
    <section>
        <h1>Indstillinger</h1>
        <fieldset>
            <div class="column">
                <div class="formSelectWrap">
                    <label for="deliveryType1">Delivery type 2</label>
                    <select id="deliveryType1" class="formSelect">
                        <option>Value</option>
                        <option>Something</option>
                        <option>Skipperlabskovs</option>
                    </select>
                </div>
                <div class="formTextWrap">
                    <label for="title2">Title 3</label>
                    <input type="text" class="formText" id="title2" />
                </div>
                <div class="formSelectWrap">
                    <label for="containertype2">Container type 3</label>
                    <select id="containerType2" class="formSelect">
                        <option>Agent Group 10</option>
                        <option>Agent Group 20</option>
                        <option>Agent Group 30</option>
                    </select>
                </div>
                <div class="formSelectWrap">
                    <label for="deliveryType3">Delivery type 4</label>
                    <select id="deliveryType3" class="formSelect">
                        <option>Value</option>
                        <option>Something</option>
                        <option>Skipperlabskovs</option>
                    </select>
                </div>
            </div>
        </fieldset>
    </section>
    <section>
        <h1>Indstillinger</h1>
        <table class="savedSearches" border="0" cellspacing="0">
            <thead>
                <tr>
                    <th class="name" style="width:50px"><span></span></th>
                    <th class="rename"><span>Marker</span></th>
                    <th class="delete"><span>Beskrivelse</span></th>
                </tr>
            </thead>
            <tbody>
{foreach from=$markers item=marker}
                <tr>
                    <td class="name"><img src="{$marker->iconUrl|escape}"></td>
                    <td class="rename" style="text-align:left"><a href="marker.php?id={$marker->id|escape}" class="fancybox600x480">{$marker->title|escape}</a></td>
                    <td class="rename" style="text-align:left">{$marker->description|escape}</td>
                </tr>
{/foreach}
            </tbody>
        </table>
        <fieldset class="formStandard">
            <div class="formSubmitWrap">
                <span class="formLink"><a href="marker.php" class="fancybox600x480">Tilføj marker</a></span>
            </div>
        </fieldset>
    </section>
                    <section class="mainSearchScript forceOpen">
                        <h1>Kort og interessepunkter</h1>
                        <fieldset>
                            <div class="column">
                                <div class="formTextWrap">
                                    <label for="title9">Title</label>
                                    <input type="text" class="formText" id="title9" />
                                </div>
                            </div>
                            <div class="column">
                                <div class="formTextWrap">
                                    <label for="title9">Title</label>
                                    <input type="text" class="formText" id="title9" />
                                </div>
                                <div class="formSelectWrap">
                                    <label for="containertype9">Container type</label>
                                    <select id="containerType9" class="formSelect">
                                        <option>Agent Group 10</option>
                                        <option>Agent Group 20</option>
                                        <option>Agent Group 30</option>
                                    </select>
                                </div>
                                <div class="formSelectWrap">
                                    <label for="country9">Country</label>
                                    <select id="country9" class="formSelect">
                                        <option>Denmark</option>
                                        <option>Germany</option>
                                        <option>England</option>
                                    </select>
                                </div>
                                <div class="formSelectWrap">
                                    <label for="deliveryType9">Delivery type</label>
                                    <select id="deliveryType9" class="formSelect">
                                        <option>Value</option>
                                        <option>Something</option>
                                        <option>Skipperlabskovs</option>
                                    </select>
                                </div>
                            </div>
                            <div class="column">
                                <div class="formCheckboxWrap">
                                    <input type="checkbox" id="something09" class="formCheckbox" />
                                    <label for="something09">Here is a checkbox</label>
                                </div>
                            </div>
                        </fieldset>
                    </section>
*}
    <section class="settingsModulesActions">
        <fieldset>
            <div class="formSubmitWrap">
                <input type="submit" value="Save and settings" class="formSubmit" />
            </div>
        </fieldset>
    </section>
</form>

                
