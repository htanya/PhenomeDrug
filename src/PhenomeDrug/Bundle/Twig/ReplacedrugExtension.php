<?php
// src/PhenomeDrug/Bundle/Twig/RemoveDrugPath.php
namespace PhenomeDrug\Bundle\Twig;



class ReplacedrugExtension extends \Twig_Extension
{

public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('removeDrugPath', array($this, 'removeDrugPathFilter')),
        );
}//closes function

    public function removeDrugPathFilter($text)
	{
$search= '';
$search = '/http.+indication\//'; //changed to "indication" instead of drug
    // replace non letter or digits by -
    		$text = preg_replace($search, '', $text);
		return $text;
	} //closes function

 public function getName()
    {
        return 'removeDrugPath_extension';
    }

} //closes class

