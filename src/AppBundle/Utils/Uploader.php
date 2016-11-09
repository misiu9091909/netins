<?php
/**
 * Created by PhpStorm.
 * User: mopielka
 * Date: 09.11.16
 * Time: 09:03
 */

namespace AppBundle\Utils;


use Symfony\Component\HttpFoundation\File\UploadedFile;

class Uploader
{
    const DEFAULT_EXT = 'jpg';
    private $targetDir;

    public function __construct($targetDir)
    {
        $this->targetDir = $targetDir;
    }

    public function upload($file)
    {
        $fileName = md5(uniqid()) . '.' . $file->guessExtension() ?: self::DEFAULT_EXT;
        $file->move($this->targetDir, $fileName);
        return $fileName;
    }
}