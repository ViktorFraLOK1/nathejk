{* Searches should have their data submitted with the form every time *}
{if $pane->getType() == 'search'}
    {include file='search/form.tpl' form=$pane->getForm() mode='hidden'}
{/if}

{include file='pane/include/header.tpl' pane=$pane}
{include file='result/grid.tpl' itemList=$pane->getItems() columnNames=$pane->getColumnNames() paneNumber=$pane->getPaneId()}
{include file='pane/include/footer.tpl'}