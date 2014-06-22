{extends file="page.tpl"}
{block name=titleText}{$agenda->title|escape}{/block}
{block name=container}
    <div class="container">
      <div class="page-header">
        <h1>Tilmelding <small>bekræft dine tilmeldingsoplysninger</small></h1>
      </div>
      <div class="row">
        <div class="span4">
          <h3>E-mail eller SMS</h3>
          <p>Vi har sendt dig en e-mail med et link og en SMS med en aktiveringskode, benyt en af delene til at bekræfte dine kontaktoplysninger og komme videre med tilmeldingen.</p>
        </div>
        <div class="span8">
          <div class="well">
            <form class="form-horizontal" style="margin:0;" action="verify" method="post">
              <fieldset>
                <input type="hidden" name="teamId" value="{$team->id|escape}">
                <div class="control-group">
                  <label class="control-label">Mobiltelefon</label>
                  <div class="controls">
                    <span class="input-xlarge uneditable-input"><strong>{$team->contactPhone|escape}</strong></span>
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label">Aktivering</label>
                  <div class="controls">
                    <input class="input-xlarge focused" name="phoneVerifyCode"  value="" placeholder="kode" type="text">
                  </div>
                </div>
                <div class="form-actions" style="background-color:inherit;padding-bottom:0; margin-bottom:0;">
                  <button type="submit" class="btn btn-success">Bekræft &raquo;</button>
                </div>
              </fieldset>
            </form>
          </div>
        </div>
      </div>

    </div> <!-- /container -->
{/block}

