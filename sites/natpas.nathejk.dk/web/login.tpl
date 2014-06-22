                <form action="" class="loginForm" method="post">
                    <input type="hidden" name="goto" value="{if isset($smarty.get.goto)}{$smarty.get.goto|escape}{/if}">
                    <section>
                        <header>
                            <h1>Log ind for at fortsætte</h1>
                        </header>
                        <fieldset class="formStandard">
                            {if isset($systemMessage)}
                                <div class="systemMessage">
                                    <p class="systemMessageHeader">{$systemMessageHeader}</p>
                                    <p>{$systemMessage}</p>
                                </div>
                            {/if}
                            {if isset($postUsers)}
                            <div class="formTextWrap">
                                <label for="email">Angiv post:</label>
                                {html_options name=post options=$postUsers}
                            </div>
                            {else}
                            <div class="formTextWrap">
                                <label for="email">Brugernavn</label>
                                <input type="text" class="formText" id="email" name="enterValidatorFormUsername" />
                            </div>
                            <div class="formPasswordWrap">
                                <label for="pass">Password</label>
                                <input type="password" class="formPassword" id="pass" name="enterValidatorFormPassword" />
                            </div>
                            {/if}
                            {*
                            <div class="formCheckboxWrap">
                                <input type="checkbox" id="remember" class="formCheckbox" />
                                <label for="remember">Remember me</label>
                            </div>
                            *}
                            <div class="formSubmitWrap">
                                <input type="submit" value="Log in" class="formSubmit" />
                                {*<span class="formLink"><a href="lostpass.php">Forgot your password?</a></span>*}
                            </div>
                            
                        </fieldset>
                    </section>
                </form>
