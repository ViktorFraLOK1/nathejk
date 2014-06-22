<header>
    <h1>Indbetalinger</h1>
</header>

<section class="fancyboxContent">
    <div class="uploadWrap">
        <section class="containerDetails">
            <div class="containerForm">
                
                <form action="" method="post">
                                                    
                    <fieldset class="batchEdit">
                        <table cellspacing="0" border="0">
                            <thead>
                                <tr>
                                    <th style="width:60px">Nr.</th>
                                    <th>Hold</th>
                                    <th style="width:100px">Totalbeløb</th>
                                    <th style="width:100px">Betalt til dato</th>
                                    <th style="width:100px">Indbetaling</th>
                                    <th style="width:100px">Dato</th>
                                </tr>
                            </thead>
                            <tbody>
                                {foreach from=$teams key=i item=team}
                                <tr>
                                    <td class="">
                                        <div class="formTextWrap">
                                            <label for="someItem00-{$i|escape}">Id</label>
                                            <span>{$team->id|escape}. {if $team->teamNumber}({$team->teamNumber|intval}.){/if}</span>
                                        </div>
                                    </td>
                                    <td class="container" style="width:auto">{$team->title|escape} <span>({$team->contactTitle|escape})</span></td>
                                    <td class="">
                                        <div class="formTextWrap" style="text-align:right">
                                            <label for="someItem01-{$i|escape}">MC Title, row 1</label>
                                            <span>{$team->totalPrice|intval},-</span>
                                        </div>
                                    </td>
                                    <td class="">
                                        <div class="formTextWrap" style="text-align:right">
                                            <label for="someItem02-{$i|escape}">Duration, row 1</label>
                                            <span>{$team->paidPrice|intval},-</span>
                                        </div>
                                    </td>
                                    <td class="">
                                        <div class="formTextWrap">
                                            <label for="someItem03-{$i|escape}">indbetalt beløb</label>
                                            <input type="text" class="formText" id="someItem03-{$i|escape}" name="team[{$team->id|escape}][paid]" style="text-align:right" value="{$team->totalPrice-$team->paidPrice}" />
                                        </div>
                                    </td>
                                    <td>
                                        <div class="formTextWrap"> 
                                            <label for="dateaf25-{$i|escape}">indebetalingsdato</label> 
                                            <input type="text" class="formText datePicker" id="dateaf25-{$i|escape}" name="team[{$team->id|escape}][date]" value="{$smarty.now|date_format:'%d/%m/%Y'}" /> 
                                        </div>
                                    </td>
                                </tr>
                                {/foreach}
                            </tbody>
                        </table>
                    </fieldset>
                    
                    <fieldset class="formStandard">
                        <div class="formSubmitWrap">
                            <input type="submit" value="Save" name="save" class="formSubmit" />
                            <span class="formLink"><a href="#" class="cancelLink">Cancel</a></span>
                        </div>
                    </fieldset>

                </form>

            </div>
        </section>
        
    </div>
</section>
