<form action="" class="loginForm" method="post">
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
            <p>Type your email address Lorem ipsum dolor sit amet, consectetur adipisicing elit</p>
            <div class="formTextWrap">
                <label for="email">Email address</label>
                <input type="text" class="formText" id="email" name="email" />
            </div>
            <div class="formSubmitWrap">
                <input type="submit" value="Send Password" class="formSubmit" />
                <span class="formLink"><a href="login.php">Cancel</a></span>
            </div>
            
        </fieldset>
    </section>
</form>