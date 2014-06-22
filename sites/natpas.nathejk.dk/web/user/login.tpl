                <form action="" class="loginForm" method="post">
                    <input type="hidden" name="goto" value="{$smarty.get.goto|escape}">
                    <section>
                        <header>
                            <h1>Please log in to proceed</h1>
                        </header>
                        <fieldset class="formStandard">
                            {if $systemMessage}
                                <div class="systemMessage">
                                    <p class="systemMessageHeader">{$systemMessageHeader}</p>
                                    <p>{$systemMessage}</p>
                                </div>
                            {/if}
                            
                            <div class="formTextWrap">
                                <label for="email">Email address</label>
                                <input type="text" class="formText" id="email" name="enterValidatorFormUsername" />
                            </div>
                            <div class="formPasswordWrap">
                                <label for="pass">Password</label>
                                <input type="password" class="formPassword" id="pass" name="enterValidatorFormPassword" />
                            </div>
                            {*
                            <div class="formCheckboxWrap">
                                <input type="checkbox" id="remember" class="formCheckbox" />
                                <label for="remember">Remember me</label>
                            </div>
                            *}
                            <div class="formSubmitWrap">
                                <input type="submit" value="Log in" class="formSubmit" />
                                <span class="formLink"><a href="lostpass.php">Forgot your password?</a></span>
                            </div>
                            
                        </fieldset>
                    </section>
                </form>