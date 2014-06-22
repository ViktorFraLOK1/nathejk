                <div id="searchResults" class="dualColumn">
                    
                    <form action="#" method="post">
                       
                            <div class="column">
                                <header>
                                    <h1>Kontrolpunkter</h1>
                                    <div class="formTools">
                                        <ul>
                                            <!-- li><input type="submit" value="Refresh" class="refresh" /></li -->
                                            <li><a href="post.php" class="fancybox800x600 icon16x16brick-add" title="Nyt kontrolpunkt">Nyt kontrolpunkt</a></li>
                                        </ul>
                                        
                                    </div>
                                </header>
                                <div class="wrap">
                                {if isset($posts)}
                                    <table cellspacing="0" border="0" class="toolTable">
                                    <thead>
                                        <tr>
                                            <th class="formCheckboxWrap"></th>
                                            <th class="title">Title</th>
                                            <th class="">Kontakt</th>
                                            <th class="linked">Uset</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- 
                                        
                                        dev note:
                                        if the checkbox is marked, the class "active" must be added to the <tr>
                                        
                                         -->
                                        {foreach from=$posts item=post}
                                        <tr>
                                            <td class="formCheckboxWrap">
                                                <input type="checkbox" class="formCheckbox" id="c01" />
                                            </td>
                                            <td class="title">
                                                <a href="post.php?teamId={$post->id|escape}" class="fancybox800x600">{$post->gruppe|escape} <span>{$post->title|escape}</span></a>
                                            </td>
                                            <td class="sbsId"><a href="">{$post->contactedTeams|@count}</a></td>
                                            <td class="sbsId"><a href="?postId={$post->id|escape}&amp;state=before">{$post->beforeCount|escape}</a></td>
                                        </tr>
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
                                </div>
                                
                            </div>
                                
                                
                            <!-- second column  -->
                            <div class="column">
                                <header>
                                    <div class="formSelectWrap" style="display:none">
                                        <select class="formSelect">
                                            <option>Columns:</option>
                                            <option>------------</option>
                                            <option>Music Cue View</option>
                                            <option>Script View</option>
                                            <option>TX Date View</option>
                                        </select>
                                    </div>
                                    <div class="formSelectWrap" style="display:none">
                                        <select class="formSelect">
                                            <option>Pane:</option>
                                            <option>------------</option>
                                            <option>New Search</option>
                                            <option>Saved Searches</option>
                                        </select>
                                    </div>
                                    <h1>Patruljer</h1>
                                </header>
                                <div class="wrap">
                                    
                                    <table cellspacing="0" border="0" class="toolTable">
                                    <thead>
                                        <tr>
                                            <th class="formCheckboxWrap"></th>
                                            <th class="title">Nr.</th>
                                            <th class="type">Patrulje</th>
                                            <th class="linked">Tid</th>
                                            <th class="date">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- 
                                        
                                        dev note:
                                        if the checkbox is marked, the class "active" must be added to the <tr>
                                        
                                         -->
                                        {if isset($post)}
                                        {foreach from=$post->contactedTeams item=team}
                                        <tr>
                                            <td class="formCheckboxWrap">
                                                <input type="checkbox" class="formCheckbox" id="c01" />
                                            </td>
                                            <td class="title">
                                                <span class="mc">{$team->teamNumber|escape}-{$team->memberCount|escape}</span>
                                            </td>
                                            <td class="type">
                                                <a href="{$team->url|escape}">{$team->title|escape} </a>
                                            </td>
                                            <td class="linked"><span class="icon16x16link" title="Linked with something and something else">Linked with something and something else</span></td>
                                            <td class="date">2010-11-09</td><!-- which date?? -->
                                        </tr>
                                        {/foreach}
                                        {/if}
                                    </tbody>
                                </table>                                   
                                    
                                </div>
                                
                            </div>
                            
                          <!--  end second column --                                
                        <nav class="toolTableTools">
                            <ul>
                                <li><a href="link.html" class="confirm icon16x16link">link</a></li>
                                <li><a href="unlink.html" class="confirm icon16x16unlink">unlink</a></li>
                                <li><a href="#" class="icon16x16edit">edit</a></li>
                                <li><a href="combine.html" class="confirm icon16x16combine">combine</a></li>
                                <li><a href="delete.html" class="confirm icon16x16delete">delete</a></li>
                                <li><a href="merge.html" class="confirm icon16x16merge">merge</a></li>
                                <li><a href="#" class="confirm icon16x16search2">search</a></li>
                                <li><a href="thumbsup.html" class="confirm icon16x16thumbsup">handled</a></li>
                            </ul>
                        </nav> -->
                                            
                        <footer></footer>   
                                
                        
                            
                    </form>
                        
                    
                    
                </div><!-- end #searchResults -->
                
            </div>
