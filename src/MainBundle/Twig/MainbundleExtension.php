<?php
// src/MainBundle/Twig/MainbundleExtension.php
namespace MainBundle\Twig;

class MainbundleExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'checkinarray' => new \Twig_Filter_Method($this, 'checkinarrayFilter'),
        );
    }

    public function checkinarrayFilter($array_details,$value)
    {
        if(in_array($value,$array_details)) {
            return true;
        } else {
            return false;
        }
    }

    public function getName()
    {
        return 'mainbundle_extension';
    }
}