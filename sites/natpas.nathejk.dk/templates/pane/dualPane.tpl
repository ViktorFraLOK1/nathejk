<div id="searchResults" class="dualColumn">
    <form action="?" method="post">
        
        <div class="column">
            
            {if $primaryPane->getDisplayItems()}
                {include file='pane/include/result.tpl' pane=$primaryPane}
            {elseif $primaryPane->getType() == 'search'}
                {include file='pane/include/search.tpl' pane=$primaryPane}
            {/if}
            
        </div>
        
        <div class="column">
        
            {if $secondaryPane->getDisplayItems()}
                {include file='pane/include/result.tpl' pane=$secondaryPane}
            {elseif $secondaryPane->getType() == 'search'}
                {include file='pane/include/search.tpl' pane=$secondaryPane}
            {/if}
            
        </div>
        
        {include file='pane/include/toolbar.tpl'}
        
        <footer>
            <input type="submit" name="removePane" value="Remove pane">
        </footer>
        
        <input type="hidden" name="panes" value="{$panes|escape}">
        
    </form>
</div>