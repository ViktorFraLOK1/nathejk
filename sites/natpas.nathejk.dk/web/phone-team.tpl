<script src="/vendor/knockoutjs/build/output/knockout-latest.js"></script>
{if isset($redirectUrl)}
    <script type="text/javascript">
        window.parent.location.href = '{$redirectUrl|escape}';
    </script>
{else}
    <header>
        <h1>Administrer grupper</h1>
    </header>

    <section class="fancyboxContent">
                

                    <form action="" method="post" data-bind="submit:save">
                        <fieldset class="batchEdit">
                            <table data-bind="console:teams()">
                                <thead>
                                    <tr><th>Hold</th><th>Organisatorisk placering</th></tr>
                                </thead>
                                <tbody data-bind="foreach:teams">
                                    <tr>
                                        <td><input type="text" data-bind="value:name" /></td>
                                        <td><select data-bind="options:$root.groups,value:type,optionsText:'title', optionsValue:'groupName'"></select></td>
                                    </tr>
                                </tbody>
                            </table>
                        </fieldset>

            <fieldset class="formStandard">
                <div class="formSubmitWrap">
                    <input type="submit" value="Gem" class="formSubmit" />
                    <input type="button" value="Tilføj" class="formSubmit"  data-bind='click: $root.addTeam'/>
                </div>
            </fieldset>
                    </form>
    </section>
    <script src="phone-team.js"></script>
{/if}
