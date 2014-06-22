                <div class="savedSearchesWrap">

                    <section>
                        <h1>Saved Searches</h1>
                        
                        <table class="savedSearches" border="0" cellspacing="0">
                            <thead>
                                <tr>
                                    <th class="name">Name</th>
                                    <th class="rename"><span>Rename</span></th>
                                    {*<th class="edit"><span>Edit</span></th>*}
                                    <th class="delete">Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                {foreach from=$savedSearches item='savedSearch'}
                                <tr>
                                    <td class="name"><a href="/search/?id={$savedSearch->id|escape}">{$savedSearch->title|escape}</a></td>
                                    <td class="rename"><a href="/search/saved/edit.php?id={$savedSearch->id|escape}" class="fancybox600x480">Rename</a></td>
                                    {*
                                    <td class="edit"><a href="/search/saved/edit.php?id={$savedSearch->id|escape}" class="fancybox600x480 icon16x16edit">Rename</a></td>
                                    *}
                                    <td class="delete"><a href="/search/saved/delete.php?id={$savedSearch->id|escape}" class="fancybox600x480 icon16x16delete">delete</a></td>
                                </tr>
                                {/foreach}
                            </tbody>
                        </table>
                    </section>
                </div>
                {*
                <div class="savedSearchesWrap">
                    <section>
                        <h1>SWAT Settings</h1>

                        <table class="savedSearches" border="0" cellspacing="0">
                            <thead>
                                <tr>
                                    <th class="name">Name</th>
                                    <th class="edit">Edit</th>
                                </tr>
                            </thead>
                            <tbody>
                            {foreach from=$swatSettings item='setting'}
                                <tr>
                                    <td class="name">{$setting->id|escape}</td>
                                    <td class="edit"><a href="editSetting.php?id={$setting->id|escape}" class="fancybox600x480 icon16x16edit">Edit</a></td>
                                </tr>
                            {/foreach}
                            </tbody>
                        </table>
                    </section>
                
                </div>
                *}
