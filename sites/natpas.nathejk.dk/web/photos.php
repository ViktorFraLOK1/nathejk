<?php
if (isset($_POST['scanDir'])) {
    $dir = '/var/www/nathejk.dk/upload';
    if (is_dir($dir)) {
        if ($handle = opendir($dir)) {
            /* This is the correct way to loop over the directory. */
            while (false !== ($file = readdir($handle))) {
                if ($im = @imagecreatefromstring('' . file_get_contents("$dir/$file"))) {
                    $dst = imagecreatetruecolor(640, 427);
                    if (imagecopyresized($dst, $im, 0, 0, 0, 0, imagesx($dst), imagesy($dst), imagesx($im), imagesy($im))) {
                        ob_start();
                        imagejpeg($dst, NULL, 75);
                        $content = ob_get_contents();
                        ob_end_clean();

                        $photo = new Nathejk_Photo;
                        $photo->source = $content;
                        if ($photo->save()) {
                            unlink("$dir/$file");
                        }
                    }
                }
            }
            closedir($handle);
        }
    }
    Pasta_Http::exitWithRedirect('');
}
if (isset($_POST['photo'])) {
    foreach ($_POST['photo'] as $photoId => $teamNumber) {
        if (intval($teamNumber) == 0) {
            if ($teamNumber == 'x') {
                $query = new Nathejk_Photo;
                $query->id = $photoId;
                $photo = $query->findOne();
                $photo->deleteUts = time();
                $photo->save();
            }
            continue;
        }
        $query = new Nathejk_Patrulje;
        $query->teamNumber = intval($teamNumber);
        $team = $query->findOne();
        if (!$team) {
            continue;
        }
        $team->photoId = $photoId;
        $team->photoUts = time();
        $team->save();
        $photo = $team->photo;
        $photo->teamId = $team->id;
        $photo->save();

        //$file = file_get_contents('http://tilmelding.nathejk.dk/system/functions/export.natpas.pdf.php?ids=' . $team->id);
        //file_put_contents('/home/nathejk/natpas/patrulje-' . $team->teamNumber . '.pdf', $file);
    }
    Pasta_Http::exitWithRedirect('');
}

$query = new Nathejk_Photo;
$query->teamId = 0;
$query->memberId = 0;
$query->deleteUts = 0;
$query->setLimit(12);
$query->setOrderBySql('id DESC');
$photos = $query->findAll();

$PAGE = new Nathejk_Page;
$PAGE->assign('activePhoto', true);
$PAGE->assign(compact('photos'));
$PAGE->display();

?>
