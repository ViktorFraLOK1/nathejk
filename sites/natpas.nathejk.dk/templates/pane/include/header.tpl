{* Get the pane namespace for columns views *}
{assign var='namespace' value=$pane->getNamespace()}

<header>
    
    <h1>{$pane->getTitle()|truncate:30|escape}</h1>
    
    <div class="formSelectWrap doSubmit"> 
        <select class="formSelect doSubmit" name="{$namespace->getNamespaceString('columnView')|escape}"> 
            <option>Columns:</option>
            {foreach from=$columnViewNames item='columnViewName'}
                <option value="{$columnViewName|escape}" {if $namespace->getValue('columnView') eq $columnViewName}selected='selected'{/if}>
                    &nbsp; {$columnViewName|escape}
                </option>
            {/foreach}
        </select> 
    </div> 
    
    
    {if $pane->getType() == 'search'}
        
        <div class="formSelectWrap">
            <select class="formSelect doSubmit" name="{$namespace->getNamespaceString('searchOption')|escape}">
                <option>Search:</option>
                <option value="new">&nbsp; New</option>
                <option value="edit">&nbsp; Edit</option>
                {if $savedSearches|@count}
                    <option>Saved Searches:</option>
                    {foreach from=$savedSearches item='savedSearch'}
                        <option value="{$savedSearch->id|escape}">
                            &nbsp; {$savedSearch->title|truncate:30|escape}
                        </option>
                    {/foreach}
                {/if}
            </select> 
        </div>
        
        {if $namespace->getValue('submit')}
        <div class="formTools">
            <ul>
                <li>
                    <input type="submit" value="Refresh" class="refresh">
                </li>
                <li>
                    <a href="/search/saved/create.php?namespace={$namespace->getName()|escape}" class="fancybox600x480 saveSearch icon16x16save">
                        Save Search
                    </a>
                </li>
            </ul>
        </div>
        {/if}
        
    {/if}
    
</header> 
<div class="wrap">

{*
{if $pane->getType() == 'search' && $namespace->getValue('submit')}
    {assign var='form' value=$pane->getForm()}
    {assign var='query' value=$form->getSolrQuery()}
    <pre style="margin: 0; padding: 10px;">{$query->getQueryString()}</pre>
{/if}
*}