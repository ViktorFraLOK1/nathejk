{if $inboxPane && $searchPane}
    {include file='pane/dualPane.tpl' primaryPane=$inboxPane secondaryPane=$searchPane}
{else}
    {include file='pane/singlePane.tpl' pane=$inboxPane}
{/if}