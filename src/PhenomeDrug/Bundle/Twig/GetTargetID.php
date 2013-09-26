<?php
// src/PhenomeDrug/Bundle/Twig/GetTargetID.php
namespace PhenomeDrug\Bundle\Twig;



class GetTargetID extends \Twig_Extension
{

public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('getTargetID', array($this, 'getTargetIDFilter')),
        );
}//closes function

    public function getTargetIDFilter($text)
	{
$search= '';
$search = '/\d{2,4}/';
    // replace non letter or digits by -
    		$text = preg_match($search, $text, $match);
		return $match[0];
	} //closes function

 public function getName()
    {
        return 'getTargetID_extension';
    }

} //closes class

