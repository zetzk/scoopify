<?php


$containerBuilder = (new  DI\ContainerBuilder());

$containerBuilder->useAttributes(true);

$containerBuilder->addDefinitions(
    []
);

return $containerBuilder->build();