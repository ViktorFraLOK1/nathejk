<?php

class Nathejk_Photo extends Pasta_TableRow
{
    public static function createFromUpload($tmpname)
    {
        if (is_uploaded_file($tmpname)) {
            $content = file_get_contents($tmpname);
            if (imagecreatefromstring($content)) {
                $photo = new self;
                $photo->source = $content;
                $photo->createUts = time();
                if ($photo->save()) {
                    return $photo;
                }
            }
        }
        return null;
    }

    public function save()
    {
        if (!$this->createUts) {
            $this->createUts = time();
        }
        return parent::save();
    }
    public function getUrl()
    {
        return '/photo.image.php?id=' . $this->id;
    }
}

?>
