<header>
    <h1>SWAT Setting</h1>
</header>

<section class="fancyboxContent">
{if $message}
<div class="systemMessage">
    <p>{$message}</p>
</div>
{/if}

<form action="" method="post">
    <input type="hidden" name="id" value="{$id|escape}" />
    <h2>{$id}</h2>
    <fieldset class="formStandard">
        <div class="formTextareaWrap">
            <label for="f01">Value:</label>
            <textarea class="formTextarea" id="f01" name="value">{$value|escape}</textarea>
        </div>

    </fieldset>
    <fieldset class="formStandard">

        <div class="formSubmitWrap">
            <input type="submit" value="Save" class="formSubmit" />
            <span class="formLink"><a href="#">Cancel</a></span>
        </div>
    </fieldset>
</form>
</section>