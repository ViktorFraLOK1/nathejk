{extends file="page.tpl"}
{block name=titleText}{$agenda->title|escape}{/block}
{block name=container}
    <div class="container">
      <div class="page-header">
        <h1>Diplom <small>find dit eget</small></h1>
      </div>
      <div class="row">
        <div class="span12">
          <p class="lead">Få jeres eget Nathejk 2013-diplom. Bare indtast jeres patruljenummer og klik på "find" - så åbner jeres diplom, som er lige til at printe ud og hænge op i jeres spejderhytte!</p>
            <form class="form-inline" action="" method="get" onsubmit="location.href='/diplom/'+this.number.value; return false">
                <div class="form-group">
                    <label class="sr-only" for="exampleInputEmail2">Holdnummer</label>
                    <input style="width:100px" type="text" class="form-control input-lg" name="number" placeholder="patrulje">
                </div>
                <input style="width:100px" type="submit" class="btn btn-success btn-lg btn-block" value="find &raquo;">
            </form>        
        </div>
      </div>

    </div> <!-- /container -->
{/block}

