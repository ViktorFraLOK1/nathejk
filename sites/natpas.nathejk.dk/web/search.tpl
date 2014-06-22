            
                <form action="" method="get" class="searchModule" style="padding-top:10px;padding-bottom:20px;">
                    
                    <section class="box-white">
                        <h1 class="search">Personsøgning</h1>
                        <fieldset>
                            <div class="column">
                                <div class="formSearchWrap formStandard">
                                	<input type="text" name="q" {if isset($smarty.get.q)}value="{$smarty.get.q|escape}" {/if}class="search" id="title" placeholder="navn, e-mail, telefonnummer, patrulje, klan"/>
                                    <input type="submit" value="Søg" class="formSubmit" style="float:none; height:34px; margin-top:0px; border-radius:5px;-moz-border-radius:5px; "/>
                                </div>
                            </div>
                        </fieldset>
                    </section>
                </form>

{if $members|count}
<div id="searchResults" class="singleColumn">
                
        <div class="column">
            <header>
                <h1>Søgeresultater ({$members|@count})</h1>
            </header>
            <div class="wra">
                <table cellspacing="0" border="0" class="toolTable">
                    <thead>
                        <tr>
                            <th class="formCheckboxi Wrap"></th>
                            <th class="date">Navn</th>
                            <th class="date">E-mail</th>
                            <th class="date">Telefon</th>
                            <th class="date">Patrulje</th>
                            <th class="title">Gruppe</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- 
                        
                        dev note:
                        if the checkbox is marked, the class "active" must be added to the <tr>
                        
                         -->
                    {foreach from=$members item=member}
                        <tr>
                            <td class="formCheckboxWrap">
                                {if $member->team->typeName == 'klan'}
                                <img src="senior16x16.png">
                                {/if}
                                {if $member->team->typeName == 'patrulje'}
                                <img src="spejder16x16.png">
                                {/if}
                            </td>
                            <td class="linked">{$member->title|escape}</td>
                            <td class="linked">{$member->mail|escape}</td>
                            <td class="linked">{$member->phone|escape}</td>
                            <td class="linked"><a href="team.php?id={$member->team->id|escape}">{$member->team->title|escape}</a></td>
                            <td class="linked">{$member->team->gruppe|escape}</td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            </div>
           <footer></footer> 
        </div>
</div>
{/if}
