<?php

$request = new Nathejk_Request($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);

$control = new Nathejk_SignupControl;

$site = new Nathejk_Site($request);
$site->addRoute(new Nathejk_Route(  '',             array($control, 'front')));
$site->addRoute(Nathejk_Route::post('/verify',      array($control, 'verify')));
$site->addRoute(Nathejk_Route::get( '/senior/#:#',  array($control, 'show')));
$site->addRoute(Nathejk_Route::post('/senior/#:#',  array($control, 'save')));
$site->addRoute(Nathejk_Route::get( '/spejder/#:#', array($control, 'show')));
$site->addRoute(Nathejk_Route::post('/spejder/#:#', array($control, 'save')));
$site->addRoute(Nathejk_Route::get( '/upload/#:#:#', array($control, 'upload')));
$site->addRoute(Nathejk_Route::get( '/photo/#:#:#', array($control, 'photo')));
$site->addRoute(Nathejk_Route::get( '/liga', array($control, 'liga')));

$site->addRoute(Nathejk_Route::post( '/callback/blaatmedlem', array($control, 'callback')));

$scanControl = new Nathejk_ScanControl;
$site->addRoute(Nathejk_Route::get( '/scan/#:#',    array($scanControl, 'contact')));
$site->addRoute(Nathejk_Route::post('/scan/#:#',    array($scanControl, 'contact')));
$site->addRoute(Nathejk_Route::get( '/bandit#',     array($scanControl, 'status')));

$diplomControl = new Nathejk_DiplomControl;
$site->addRoute(Nathejk_Route::get( '/diplom',      array($diplomControl, 'find')));
$site->addRoute(Nathejk_Route::get( '/diplom/#',    array($diplomControl, 'create')));

$site->sendResponse();


