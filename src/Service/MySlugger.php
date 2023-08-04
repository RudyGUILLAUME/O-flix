<?php

namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Our slugger service
 */
class MySlugger
{
    private $slugger;
    private $toLower;

    public function __construct(SluggerInterface $slugger, bool $toLower)
    {
        $this->slugger = $slugger;
        $this->toLower = $toLower;
    }

    /**
     * Slugify a string
     * 
     * @param string $string The string to slug
     * 
     * @return string The slugified string
     */
    public function slugify(string $string)
    {

        $slug = $this->slugger->slug($string);

        if ($this->toLower === true) {
            $slug = $slug->lower();
        }

        return $slug;
    }
}