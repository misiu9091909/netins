<?php


namespace AppBundle\Utils;

class Slugger
{
    public function slugify($string)
    {
        return preg_replace('/\s+/', '-', mb_strtolower(trim(strip_tags($string)), 'UTF-8'));
    }
}
