{if $pane->getType() == 'search'}
    
    {include file='pane/include/header.tpl' pane=$pane}
    
    <div class="searchModules">
        {include file='search/form.tpl' form=$pane->getForm()}
    </div>
    
    {include file='pane/include/footer.tpl'}
    
{/if}