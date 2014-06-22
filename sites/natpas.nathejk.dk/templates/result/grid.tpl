{if $itemList}
    
    <table cellspacing="0" border="0" class="toolTable">
        <thead>
            <tr>
                <th class="formCheckboxWrap"></th>
                
                {foreach from=$columnNames item='title' key='name'}
                    {* Dates requires a specfic class to be sortable *}
                    {if isset($columnName) && ($columnName eq 'txDateUts' || $columnName eq 'deadlineUts')}
                        <th class="date">{$title}</th>
                    {else}
                        <th class="{$name}">{$title}</th>
                    {/if}
                {/foreach}
            </tr>
        </thead>
        <tbody>
            
            
            {foreach from=$itemList item='listItem'}
                
                {include file='result/row.tpl' listItem=$listItem columnNames=$columnNames paneNumber=$paneNumber}
                
            {/foreach}
            
        </tbody>
    </table>
    
{else}

    <div>
        <div class="systemMessage">
            <p class="systemMessageHeader">Nothing to display</p>
            <p>The search did not match any items</p>
        </div>
    </div>
{/if}
