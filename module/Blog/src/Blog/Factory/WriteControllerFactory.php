<?php
/*
On demande à la "factory" de renvoyer le "WriteController" et on ajoute les dépendances requises dans le constructeur.
Le "WriteController" n'existe pas encore, on va le créer, on suppose qu'il existera plus tard. On accède à "FormElementManager"
pour accéder au "PostForm". Tous les formulaires doivent être accessibles via "FormElementManager", même si on n'a pas enregistré 
le "PostForm" dans les fichiers de configuration, le "FormElementManager" connaît automatiquement les formulaires qui font office 
d'invocables. Tant qu'on n'a pas besoin de les enregistrer explicitement. La prochaine étape est la création du contrôleur.
*/
namespace Blog\Factory;

use Blog\Controller\WriteController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class WriteControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $realServiceLocator = $serviceLocator->getServiceLocator();
        $postService        = $realServiceLocator->get('Blog\Service\PostServiceInterface');
        $postInsertForm     = $realServiceLocator->get('FormElementManager')->get('Blog\Form\PostForm');

        return new WriteController(
            $postService,
            $postInsertForm
        );
    }
}