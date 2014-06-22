
<div id="searchResults" class="singleColumn">
                    
    <form action="" method="get">
        
        <div class="column">
            <header>
                <h1>Fangstoversigt ({$captures|@count})</h1>
            </header>
            <div class="wrap">
                <table cellspacing="0" border="0" class="toolTable">
                    <thead>
                        <tr>
                            <th class="formCheckboxi Wrap"></th>
                            <th class="date">check ind</th>
                            <th class="date">check ud</th>
                            <th class="title">Type</th>
                            <th class="title">Patrulje</th>

                            <th class="type">Senior</th>
                            <th class="linked">Klan</th>
                            <th class="distributor">LOK</th>
                            <th class="duration">Koordinat</th>
                            <th class="duration">Bemærkning</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- 
                        
                        dev note:
                        if the checkbox is marked, the class "active" must be added to the <tr>
                        
                         -->
                    {foreach from=$captures item=catch}
                        <tr>
                            <td class="formCheckboxWrap">
                                <input type="radio" class="formCheckbox" name="checkInId" value="{$catch->id|escape}" id="c01" />
                            </td>
                            <td class="date">{if $catch->createdUts}{$catch->createdUts|date_format:'%a. %R'|utf8_encode}{else}-{/if}</td><!-- which date?? -->
                            <td class="date">{if $catch->outUts}{$catch->outUts|date_format:'%a. %R'|utf8_encode}{else}-{/if}</td><!-- which date?? -->
                            <td class="linked">{$catch->typeName|escape}</td>
                            <td class="linked">
                                {if $catch->team}<a href="team.php?id={$catch->team->id}">{$catch->team->teamNumber}. {$catch->team->title|escape}</a>{/if}
                            </td>
                            <td class="type distId">
                                {if $catch->member && $catch->member->number}<span style="float:left">{$catch->member->number}</span>{else}
                                    {$catch->memberId}
                                {/if}
                            </td>
                            <td class="linked">{if $catch->member && $catch->member->team}<a href="team.php?id={$catch->member->team->id|escape}">{$catch->member->team->title|escape}</a>{/if}</td>
                            <td class="distributor">{if $catch->member && $catch->member->team && $catch->member->team->lokNumber}LOK {$catch->member->team->lokNumber|escape}{/if}</td>
                            <td class="duration">{$catch->location|escape}</td>
                            <td class="duration">{$catch->remark|escape}</td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            </div>
            
        </div>
                                            
        <nav class="toolTableTools">
        <ul>
            <li><a href="functions/event.php" class="fancybox600x480 confirm icon16x16thumbsup">ret</a></li>
        </ul>
        </nav>
                    
        <footer>
            <!--input type="submit" value="Add pane" /-->
        </footer>
            
    </form>
</div>
