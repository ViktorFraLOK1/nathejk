<tr>
     <td class="formCheckboxWrap">
        <input type="checkbox" class="formCheckbox" id="c{$paneNumber|escape}id{$listItem->id|escape}" name="id[{$paneNumber|escape}][]" value="{$listItem->id}"/>
    </td>
    {foreach from=$columnNames item='title' key='columnName'}
        
        <td class="{$columnName|escape}">
            {if $columnName eq 'title'}
                <a href="{$listItem->url|escape}" iclass="fancyboxFullxFull" style="text-decoration:underline;">{$listItem->getDisplayColumn('title')|escape|default:'<i> - intet navn - </i>'}</a>
            {elseif $columnName eq 'country'}
                <span class="icon16x16{$listItem->getDisplayColumn($columnName)|lower|escape}">{$listItem->getDisplayColumn($columnName)|escape}</span>
            {elseif $columnName eq 'channel'}
                <span>{$listItem->getDisplayColumn($columnName)|escape}</span>
            {* elseif $columnName eq 'type'}
                <span class="{$listItem->getDisplayColumn('type')|lower|escape}">{$listItem->displayType|escape}</span>
                *}
            {elseif $columnName eq 'unpaidPrice' || 
                    $columnName eq 'phod'}
                {if $listItem->memberCount}
                    {if !$listItem->getDisplayColumn($columnName)}
                        <span class="icon16x16checkmark">yes</span>
                    {elseif $listItem->getDisplayColumn($columnName) > 0}
                        <span style="color:#c00">- {$listItem->getDisplayColumn($columnName)}</span>
                    {else}
                        {$listItem->getDisplayColumn($columnName)}
                    {/if}
                {/if}
            {elseif $columnName eq 'checkedAtStart'}
                {if $listItem->checkedAtStart > 0}
                        <span class="icon16x16checkmark">yes</span>
                {/if}
            {elseif $columnName eq 'photoId'}
                {if $listItem->photoId > 0}
                    <a href="functions/export.natpas.pdf.php?ids={$listItem->id}"><span class="icon16x16vcard">natpas</span></a>
                {/if}
            {elseif $columnName eq 'catchCount'}
                <a href="capture.php?teamId={$listItem->id}">{$listItem->getDisplayColumn($columnName, 'search')|escape}</a>
            {elseif $columnName|substr:-3 eq 'Uts'}
                {if $listItem->getDisplayColumn($columnName, 'search') == 0}
                -
                {else}
                {$listItem->getDisplayColumn($columnName, 'search')|date_format:"%m/%d kl. %H:%M"}
                {/if}
            {else}
                {$listItem->getDisplayColumn($columnName, 'search')|truncate:50:'...'|escape}
            {/if}
        </td>
        
    {/foreach}
</tr>
