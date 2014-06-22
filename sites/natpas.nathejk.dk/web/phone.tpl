<script src="/vendor/knockoutjs/build/output/knockout-latest.js"></script>
<div class="savedSearchesWrap singleColumn">
    <div class="column">
    <section>
        <h1><i class="icon-phone"></i> Telefonliste</h1>

        <form action="" method="post" data-bind="submit:save">
        <!-- ko foreach: groups -->
            <fieldset class="formUpload" data-bind="visible:members().length > 0">
                <h2 data-bind="text:title"></h2>
                <table cellspacing="0" border="0">
                    <thead>
                        <tr>
                        <th class="artist">Name</th>
                        <th>Telefon</th>
                        <th>Funktion</th>
                        <th />
                        </tr>
                    </thead>
                    <tbody data-bind="foreach: members">
                        <tr>
                        <td class="artist" style="width:49%">
                            <div class="formTextWrap">
                                <label for="someItem01">Navn</label>
                                <input type="text" class="formText" id="someItem01" data-bind="value: name"/>
                            </div>
                        </td>
                        <td><input class='required number' data-bind='value: phone, uniqueName: true' /></td>
                        <td>
                            <select data-bind="foreach:$root.groups,value:teamId">
                            <!-- ko if: parseInt($parent.teamId()) == 0 || groupName() != 'new' -->
                                <optgroup data-bind="attr:{ label:title},foreach:teams">
                                    <option data-bind="text:name,attr:{ value:id}">funktion</option>
                                </optgroup>
                            <!-- /ko -->
                            </select>
                        </td>
                        <td><a href='#' data-bind='click: $root.removeMember'>&times;</a></td>
                        </tr>
                    </tbody>
                </table>

            </fieldset>
        <!-- /ko -->
            <fieldset class="formStandard">
                <div class="formSubmitWrap">
                    <input type="submit" value="Gem" class="formSubmit" data-bind="enable: members().length > 0"/>
                    <input type="button" value="Tilføj" class="formSubmit"  data-bind='click: $root.addMember'/>
                    <span class="formLink"><a href="phone-team.php" class="fancybox600x480"/>administrer grupper...</a></span>
                </div>
            </fieldset>
        </form>

    </section>
    </div>
</div>
                
<script src="phone.js"></script>
