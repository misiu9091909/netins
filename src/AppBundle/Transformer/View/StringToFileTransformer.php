<?php
/**
 * Created by PhpStorm.
 * User: mopielka
 * Date: 09.11.16
 * Time: 10:29
 */

namespace AppBundle\Transformer\View;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\File;

class StringToFileTransformer implements DataTransformerInterface
{
    /**
     * @var string
     */
    private $filesDir;

    /**
     * @param string $filesDir
     */
    public function __construct($filesDir = '/var/ssd/threeday/web/uploads/images')
    {
        $filesDir = rtrim($filesDir, '/') . '/';
        $this->filesDir = $filesDir;
    }

    /**
     * @param string $string
     * @return File|null
     */
    public function transform($fileName)
    {
        $path = $this->filesDir . $fileName;
        $file = null;
        if (file_exists($path))
            $file = new File($path);
        return $file;
    }

    /**
     * @param File $file
     * @return File
     */
    public function reverseTransform($file)
    {
        return $file;
    }
}