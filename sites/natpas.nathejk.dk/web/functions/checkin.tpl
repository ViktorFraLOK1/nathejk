<header>
    <h1>Check ind/ud</h1>
</header>

<section class="fancyboxContent">
    <div class="uploadWrap">
        
        <section class="containerDetails">
            <div class="containerForm">
            
                <form action="" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="formGenerateUts" value="{$smarty.now}" />
                                                    
                    <fieldset class="batchEdit">
                        <table cellspacing="0" border="0">
                            <thead>
                                <tr>
                                    <th class="container">Patruljenummer og navn</th>
                                    <th class="dist-episode">Check ind tid</th>
                                    <th class="duration">Check ud tid</th>
                                    <th class="mctitle">Kommentar</th>
                                    {if $post->title eq 'start'}<th class="mctitle">Patruljefoto</th>{/if}
                                </tr>
                            </thead>
                            <tbody>
                            {foreach from=$teams item=team}
                                {assign var=teamId value=$team->id}
                                {assign var=checkIn value=$checkIns.$teamId}
                                {assign var=nowTime value=$smarty.now|date_format:'%R'}
                                <tr>
                                    <td class="container"><span>{$team->teamNumber|escape}. </span>{$team->title|escape} <span>({$team->gruppe|escape})</span></td>
                                    <td class="dist-episode" style="width:100px">
                                        <div class="formTextWrap">
                                            <label for="someItem01">Check ind tidspunkt</label>
                                            {html_options name="team[`$team->id`][inDate]" style="width:45px" options=$days selected=$checkIn->createdUts|date_format:'%Y-%m-%d'}
                                            <input style="width:45px" type="text" class="formText" name="team[{$team->id|escape}][inTime]" value="{$checkIn->createdUts|default:$smarty.now|date_format:'%R'}" />
                                            {$checkIn->createdUts|date_format:'%Y-%m-%d'}
                                        </div>
                                    </td>
                                    <td class="duration" style="width:100px">
                                        <div class="formTextWrap">
                                            <label for="someItem02">Check ud tidspunkt</label>
                                            {html_options name="team[`$team->id`][outDate]" style="width:45px" options=$days selected=$checkIn->outUts|date_format:'%Y-%m-%d'}
                                            <input style="width:45px" type="text" class="formText" name="team[{$team->id|escape}][outTime]" value="{if $checkIn}{$nowTime}{/if}" />
                                        </div>
                                    </td>
                                    <td class="mctitle" style="width:auto">
                                        <div class="formTextWrap">
                                            <label for="someItem03">Kommentar til registrering</label>
                                            <input type="text" class="formText" id="someItem03" name="team[{$team->id}][remark]" value="{$checkIn->remark|escape}" />
                                        </div>
                                    </td>
                                    {if $post->title eq 'Start'}
                                    <td class="mctitle" style="width:auto">
                                        <div class="formFileWrap">
                                            <label for="uploader">Vælg patruljebillede</label>
                                            <input type="file" size="16" name="photo[{$team->id}]" id="uploader" />
                                        </div>
                                    </td>
                                    {/if}
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

