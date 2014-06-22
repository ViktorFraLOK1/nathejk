<?php
$markerId = isset($_GET['id']) ? $_GET['id'] : 0;

$query = new Nathejk_Marker;
$query->typeName = 'point';
$markers = $query->findAll();

$query->id = $markerId;
$marker = $query->findOne();

if ($_POST) {
    if (!$marker) {
        $marker = new Nathejk_Marker;
        $marker->typeName = 'point';
    }
    $requiredFields = array('title', 'description', 'value', 'colorName', 'iconName');
    foreach ($requiredFields as $field) {
        if (isset($_POST[$field])) {
            $marker->$field = $_POST[$field];
        }
    }
    if (isset($_POST['save']) && $marker->save()) {
        Pasta_Http::exitWithRedirect('functions/window-reload.php');
    }
    if (isset($_POST['delete']) && $marker->delete()) {
        Pasta_Http::exitWithRedirect('functions/window-reload.php');
    }
    die($marker->errorString);
}

$PAGE = new Nathejk_Page(true);
$PAGE->assign(compact('marker', 'markers'));
$PAGE->assign('iconNames', Nathejk_Marker::getIconNames());
$PAGE->assign('colorNames', Nathejk_Marker::getColorNames());
$PAGE->display();

?>
