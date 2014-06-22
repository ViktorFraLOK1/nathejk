<?php
$PAGE = new Sbs_Page(true);



$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
$value = Sbs_Swat::getValue($id);
$PAGE->assign('id', $id);

if (!empty($_POST)) {
    if (is_array($value)) {
        $value = $_POST['value'];
        $list = array();
        foreach (explode(',', $value) as $val) {
            if (trim($val)) {
                $list[] = trim($val);
            }
        }
        Sbs_Swat::setValue($id, $list);
        $value = $list;
    } else {
        Sbs_Swat::setValue($id, $_POST['value']);
    }
    $PAGE->assign('message', 'Value saved.');
}

if (is_array($value)) {
    $PAGE->assign('value', implode(', ', $value));
} else {
    $PAGE->assign('value', $value);
}

$PAGE->display();
?>