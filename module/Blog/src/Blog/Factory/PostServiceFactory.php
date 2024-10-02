<?php
/*
Il faut créer cette classe, on aura une erreur "ServiceNotFoundException" levé par le ServiceManager, disant que le service 
demandé est introuvable.
*/
namespace Blog\Factory;

use Blog\Service\PostService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PostServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new PostService(
            $serviceLocator->get('Blog\Mapper\PostMapperInterface')
        );
    }
}