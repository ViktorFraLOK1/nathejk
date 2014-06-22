<header>
    <h1>Sendt e-mail</h1>
</header>

<section class="fancyboxContent">
    <form action="" method="post" target="">

        <p style="float:right">{$mail->sendUts|date_format:'%Y-%m-%d kl. %H:%M'}</p><br style="clear:both">

        <fieldset class="formStandard">
            <div class="formTextWrapDisabled">
                <span class="pseudoLabel">Fra:</span>
                <span class="pseudoFormText">{$mail->mailFrom|escape}</span>
            </div>
            <div class="formTextWrapDisabled">
                <span class="pseudoLabel">Til:</span>
                <span class="pseudoFormText">{$mail->rcptTo|escape}</span>
            </div>
            <div class="formTextWrapDisabled">
                <span class="pseudoLabel">Emne:</span>
                <span class="pseudoFormText">{$mail->subject|escape}</span>
            </div>

        </fieldset>

        <div class="systemMessage">
            <p>{$mail->body|escape|nl2br}</p>
        </div>

        <fieldset class="formStandard">
            <div class="formSubmitWrap">
                <span class="formLink"><a href="#" class="cancelLink">Afbryd</a></span>
            </div>
        </fieldset>

    </form>
</section>

