<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Nathejk</title>

        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
        <script src="/metadesign/page.js"></script>

        <link rel="stylesheet" type="text/css" media="screen,projection,print" href="/metadesign/page.css?12">
        <link rel="shortcut icon" href="/metadesign/img/favicon.ico" type="image/x-icon" />
        <link rel="stylesheet" href="/vendor/fontawesome/css/font-awesome.min.css">

        <!--[if IE 7]>
          <link rel="stylesheet" href="/vendor/font-awesome/css/font-awesome-ie7.min.css">
        <![endif]-->
        <!--[if lt IE 9]>
        <script src="/metadesign/js/html5shiv.js"></script>
        <![endif]-->
    </head>

    <body>
        <div id="mainContainer">
            <header class="siteHeader">

                <hgroup>
                    <h1 class="logo">Nathejk</h1>
                </hgroup>

             {if $USER}
                <nav class="primary">
                    <ul>
                        <li class="home icon16x16home{if isset($activeIndex)} active{/if}"><a href="/">Home</a></li>
                        <li class="searc con16x16searc{if isset($activeSearch)} active{/if}"><a href="/search.php"><i class="icon-search icon-large"></i></a></li>
                        <li class="{if isset($activePhone)}active{/if}"><a href="/phone.php"><i class="icon-phone icon-large"></i></a></li>
                        <li class="kort{if isset($activeMap)} active{/if}"><a href="/gmap/">Kort</a></li>
                        <li class="lok{if isset($activeLok)} active{/if}"><a href="/list.php?typeName=lok">LOK</a></li>
                        <li class="post{if isset($activePost)} active{/if}"><a href="/posts.php">Poster</a></li>
                        <li class="inbox{if isset($activeSenior)} active{/if}"><a href="/list.php?typeName=senior">Senior <span title="{$activeKlanCount|escape} tilmeldte klaner">{$activeKlanCount|escape}</span>{if $pendingKlanCount} <span title="{$pendingKlanCount|escape} afventende klaner" style="background-color:#d70">{$pendingKlanCount|escape}</span>{/if}</a></li>
                        <li class="clipboard{if isset($activeSpejder)} active{/if}"><a href="/list.php?typeName=spejder">Spejder <span title="{$activePatruljeCount|escape} tilmeldte patruljer">{$activePatruljeCount|escape}</span>{if $pendingPatruljeCount} <span title="{$pendingPatruljeCount|escape} afventende patruljer" style="background-color:#d70">{$pendingPatruljeCount|escape}</span>{/if}{if $agenda->activeTeams|count} <span title="{$agenda->activeTeams|count|escape} patruljer aktive i løbet" style="background-color:#77d">{$agenda->activeTeams|count|escape}</span>{/if}</a></li>
                        <li class="capture{if isset($activeCapture)} active{/if}"><a href="/capture.php">Kontakt <span title="{$checkInCount|escape} fangster">{$checkInCount|escape}</span></a></li>
                        {if $photoCount}
                        <li class="photo{if isset($activePhoto)} active{/if}"><a href="/photos.php"><i class="icon-camera icon-large"></i> &nbsp;<span title="{$photoCount|intval} fotos" style="background-color:#d70">{$agenda->unmarkedPhotos|count}</span></a></li>

                        {/if}
                        {* <li class="savedsearches">
                            <div class="formSelectWrap">
                                <select class="formSelect">
                                    <option value='#'>Saved Searches</option>
                                    <option value='#'>--------------</option>
                                    {foreach from=$savedSearches item='savedSearch'}
                                    <option value="/search/?id={$savedSearch->id|escape}">{$savedSearch->title|escape}</option>
                                    {foreachelse}
                                    <option value='#'>... no saved searches</option>
                                    {/foreach}
                                </select>
                            </div>
                        </li> *}
                    </ul>
                </nav>

                <nav class="userOptions">
                    <ul>
                        <li><a href="/login.php?{$smarty.const.ENTER_VALIDATOR_LOGOUT}">Log ud</a></li>
                        {if isset($post)}
                        <li><a href="/login.php">{$post->title|escape}</a></li>
                        {/if}
                        <li><a href="/settings.php">Indstillinger</a></li>
                    </ul>
                </nav>
            {/if}
            </header>

            <div id="contentContainer">
