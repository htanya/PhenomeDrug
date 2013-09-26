<?php

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Parameter;
use PhenomeDrug\Bundle\Services;


$container->setDefinition(
    'sparql_query.services',
    new Definition(
        'PhenomeDrug\Bundle\Services\sparqlQueries'
		  )
			);
//));

$container
    ->register('phenome.twig.replacedrug_extension', 'PhenomeDrug\Bundle\Twig\ReplacedrugExtension')
    ->addTag('twig.extension');

$container
    ->register('phenome.twig.urlencode_extension', 'PhenomeDrug\Bundle\Twig\URLencodeExtension')
    ->addTag('twig.extension');

$container
    ->register('phenome.twig.getTargetID_extension', 'PhenomeDrug\Bundle\Twig\GetTargetID')
    ->addTag('twig.extension');

/*

$container->setDefinition(
    'phenome_drug.example',
    new Definition(
        'PhenomeDrug\Bundle\Example',
        array(
            new Reference('service_id'),
            "plain_value",
            new Parameter('parameter_name'),
        )
    )
);

*/
