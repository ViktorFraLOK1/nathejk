{if $pane->getDisplayItems()}
    
    <div id="searchResults" class="singleColumn">
    
        <form action="" method="post">
            
            <div class="column">
                {include file='pane/include/result.tpl' pane=$pane}
            </div>
            {include file='pane/include/toolbar.tpl'}
            
            <footer>
                <!-- input type="submit" name="addPane" value="Add pane"-->
            </footer>
            <input type="hidden" name="panes" value="{$panes|escape}">
            
        </form>
    
    </div>
    
{elseif $pane->getType() == 'search'}
    
    <form action="?" method="post" class="searchModules">
        
        {* This rather annoying single pane search needs to include the column view all other panes gets it from the header *}
        {assign var='namespace' value=$pane->getNamespace()}
        <input type="hidden" name="{$namespace->getNamespaceString('columnView')|escape}" value="{$namespace->getValue('columnView')|escape}">
        
        {include file='search/form.tpl' form=$pane->getForm()}
    </form>
    
{/if}
