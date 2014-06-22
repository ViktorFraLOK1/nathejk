
<div class="savedSearchesWrap singleColumn">
    <div class="column">
    <section>
        <h1>Overblik</h1>
        
        <table class="savedSearches" border="0" cellspacing="0">
            <thead>
                <tr>
                    <th class="name"><span>&nbsp;</span></th>
                    <th class="rename"><span>Hold</span></th>
                    <th class="rename"><span>Startet</span></th>
                    <th class="rename"><span>Aktive</span></th>
                    <th class="rename"><span>Udgået ej hentet</span></th>
                    <th class="rename"><span>Afhentet</span></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="name">Seniorer</td>
                    <td class="rename">{$klanCount|escape}</td>
                    <td class="edit">{$seniorCount|escape}</td>
                    <td colspan=3></td>
                </tr>
                <tr>
                    <td class="name">Spejdere</td>
                    <td class="rename">{$patruljeCount|escape}</td>
                    <td class="edit">{$spejderCount|escape}</td>
                    <td class="edit">{$activeSpejderCount|escape}</td>
                    <td class="edit">{$pausedSpejderCount}</td>
                    <td class="edit">{$stoppedSpejderCount|escape}</td>
                </tr>
            </tbody>
        </table>

        <h1>Patruljer der ikke er i mål ({$agenda->getTeamsNotSeenOn('slut')|@count})</h1>
        
        <table class="savedSearches" border="0" cellspacing="0">
            <thead>
                <tr>
                    <th class="name"><span>Patrulje</span></th>
                    <th class="rename"><span>Sammenlagt</span></th>
                    <th class="rename"><span>Personer</span></th>
                    <th class="rename"><span>Kontakt</span></th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$agenda->getTeamsNotSeenOn('slut') item=team}
                <tr>
                    <td class="name">{$team->teamNumber}. <a href="{$team->url|escape}">{$team->title|escape}</a></td>
                    <td class="rename">{$team->parentTeamId}</td>
                    <td class="edit">{$team->activeMemberCount}</td>
                    <td class="et"><a href="capture.php?teamId={$team->id|escape}">{$team->contactCount|escape} kontakter</a></td>
                </tr>
                {/foreach}
            </tbody>
        </table>
        <h1>Bingo ({$agenda->bingoTeams|@count})</h1>
        
        <table class="savedSearches" border="0" cellspacing="0">
            <thead>
                <tr>
                    <th class="name"><span>Patrulje</span></th>
                    <th class="rename"><span>Sammenlagt</span></th>
                    <th class="rename"><span>Personer</span></th>
                    <th class="rename"><span>Kontakt</span></th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$agenda->bingoTeams item=team}
                <tr>
                    <td class="name">{$team->teamNumber}. <a href="{$team->url|escape}">{$team->title|escape}</a></td>
                    <td class="rename">{$team->parentTeamId}</td>
                    <td class="edit">{$team->activeMemberCount}</td>
                    <td class="et"><a href="capture.php?teamId={$team->id|escape}">{$team->contactCount|escape} kontakter</a></td>
                </tr>
                {/foreach}
            </tbody>
        </table>
        <h1>Udgåede spejdere, ikke afhentet ({$agenda->pausedMembers|@count})</h1>
        
        <table class="savedSearches" border="0" cellspacing="0">
            <thead>
                <tr>
                    <th class="name"><span>Navn</span></th>
                    <th class="rename"><span>Udgået</span></th>
                    <th class="rename"><span>Patrulje</span></th>
                    <th class="rename"><span>Kontakt</span></th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$agenda->pausedMembers item=member}
                <tr>
                    <td class="name"><a href="member.php?id={$member->id|escape}" class="fancybox600x520">{$member->title|escape}</a></td>
                    <td class="edit">{$member->pausedUts|date_format:'%Y-%m-%d kl. %H:%M'}</td>
                    <td class="name">{$member->team->teamNumber}. <a href="{$member->team->url|escape}">{$member->team->title|escape}</a></td>
                    <td class="et"><a href="capture.php?teamId={$member->team->id|escape}">{$member->team->contactCount|escape} kontakter</a></td>
                </tr>
                {/foreach}
            </tbody>
        </table>
    </section>
    </div>
</div>


