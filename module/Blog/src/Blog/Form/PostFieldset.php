<?php
/*
Utiliser les formulaires et les ensembles de champs 
***************************************************
Jusqu'à présent, on n'a fait que lire les données de la base de données. Dans une réelle application, cela ne mène pas loin car 
très souvent, le moins qu'on ait à faire c'est prendre en charge les opérations complètes de création, de lecture, de mise à jours et 
de suppresion (CRUD). Le plus souvent, le processus d'obtention des données dans la base de données est qu'un utilisateur entrre les données 
dans un formulaire web et l'application utilise ensuite l'entrée utilisateur et l'enregistre dans le backend. 

Zend\Form\Fieldset
******************
C'est le premier composant à connaître, il contient un ensemble d'éléments réutilisables. On utilisera le Fieldset pour créer l'entrée pour 
les modèles d'arrière-plan, il est considéré comme une bonne pratique d'avoir un "Fieldset" pour chaque modèle de l'application. Ce composant 
n'est pas un formulaire, il ne pourra pas utiliser un "Fieldset" sans l'attacher au composant "Formulaire". L'avantage est qu'on dispose d'un 
ensemble d'éléments qu'on peut réutiliser pour autant de formulaires qu'on le souhaite sans avoir à déclarer à nouveau toutes les entrées pour le modèle 
par le "Fieldset". Le composant principal dont on a besoin est "Zend\Form", c'est le conteneur principal pour tous les éléments du formulaire web. On 
peut également ajouter des éléments uniques ou un ensemble d'éléments sous la forme d'un "Fieldset".
*/ 
namespace Blog\Form;

use Blog\Model\Post;
use Zend\Form\Fieldset;
use Zend\Stdlib\Hydrator\ClassMethods;

class PostFieldset extends Fieldset
{
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->setHydrator(new ClassMethods(false));
        $this->setObject(new Post());

        $this->add(array(
            'type' => 'hidden',			
            'name' => 'id'
        ));
		
		$this->add(array(
            'type' => 'textarea',
            'name' => 'title',
            'options' => array(
                'label' => 'Titre du sujet '
            )
        ));
		
        $this->add(array(
            'type' => 'textarea',
            'name' => 'text',
            'options' => array(
                'label' => 'Sujet '
            )
        ));        
    }
}