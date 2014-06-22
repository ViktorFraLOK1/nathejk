<!DOCTYPE html>
<html lang="da">
  <head>
    <meta charset="utf-8">
    <title>{block name=titleText}Nathejk{/block}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <link href="/source/css/main.css" rel="stylesheet">
 
    <!-- Le javascript
    ================================================== -->
    <!-- Latest compiled and minified JavaScript -->
    <script type="text/javascript" src="/vendor/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="/vendor/bootstrap/docs/assets/js/bootstrap.min.js"></script>

    <script type="text/javascript" src="/vendor/jquery-validation/dist/jquery.validate.min.js"></script>
    <script type="text/javascript" src="/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
    
    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le fav and touch icons -->
    <link href="http://nathejk.dk/favicon.ico" rel="shortcut icon" type="image/x-icon" />
  </head>

  {block name=body}
  <body>

    {block name=navbar}
    <div class="navbar navbar-fixed-top navbar-inverse">
      <div class="navbar-inner">
        <div class="container">
          <a class="brand" href="#" style="background:url(/assets/img/logomoonsmall.png) no-repeat; padding-left:60px;">{block name=titleText}Nathejk{/block}</a>
          <div class="nav-collapse">
            <ul class="nav">
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>
    {/block}

    {block name=container}
    <div class="container"></div> <!-- /container -->
    {/block}

  </body>
  {/block}
</html>
