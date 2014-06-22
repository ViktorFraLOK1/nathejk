{extends file="page.tpl"}
{block name=titleText}{$agenda->title|escape}{/block}
{block name=container}
    <div class="container">
      <header class="jumbotron masthead">
        <div class="inner">
          <p>Tilmeldingen til <strong>Nathejk 2013</strong> åbner om</p>
          <h1 id="countdown">&times;<small>dage</small> &times;<small>timer</small> &times;<small>minutter</small> &times;<small>sekunder</small></h1>
        </div>
      </header>
      <div class="row">
        <div class="span4">
            <div class="well alert alert-info">
<h1>PATRULJER <small>12-17 år</small></h1>
{$agenda->spejderIntro|escape|nl2br}
            </div>
        </div>
        <div class="span4">
            <img src="/files/collage2013.png">
        </div>
        <div class="span4">
          <div class="well alert alert-error">
<h1>SENIORER <small>+15 år</small></h1>
{$agenda->seniorIntro|escape|nl2br}
          </div>
        </div>
      </div>

<script src="/jquery/countdown.min.js"></script>
<script>
$('#countdown').countdown({
    until: new Date({$agenda->signupStartUts|intval}*1000),
{literal}
    layout: '{dn}<small>{dl}</small> {hn}<small>{hl}</small> {mn}<small>{ml}</small> {sn}<small>{sl}</small>',
{/literal}
    onTick: function(periods) { if ($.countdown.periodsToSeconds(periods) == 10) { $(this).addClass('highlight');}}, 
    expiryUrl: '/', 
    labels: ['År', 'Måneder', 'Uger', 'dage', 'timer', 'minutter', 'sekunder'],
    labels1: ['År', 'Måned', 'Uge', 'dag', 'time', 'minut', 'sekund'],
});
</script>
    </div> <!-- /container -->
{/block}

