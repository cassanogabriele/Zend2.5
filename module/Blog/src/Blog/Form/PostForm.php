<?php
/*
On crée tous les formulaires dont on a besoin pour terminer le module "Blog", on commence par créer un "Fieldset" qui contient tous les éléments 
d'entrée dont on a besoin pour travailler avec les données de blog. On aura besoin d'une entrée masquée pour la propriété "id", qui n'est nécessaire que 
pour éditer et supprimer des données. On aura besoin d'une entrée de texte pour la propriété "text", on aura besoin d'une entrée de texte pour la propriété 
"title". On ajoute un "PostField" au formulaire, on a ajoute un bouton d'envoi au formulaire, on va utiliser le formulaire.

Ajouter un nouveau message 
**************************
Avant d'utiliser le formulaire, il y a encore des tâches à accomplir : 

- créer un nouveau contrôleur "WriteController" 
- ajouter "PostService" en tant que dépendance au "WriteController"
- ajouter "PostForm" en tant que dépendance au "WriteController"
- créer un nouveau blog d'itinéraire, ajouter cet itinéraire au "WriteController" et son "addAction()"
- créer une nouvelle vue qui affiche le formulaire

Après correction, le "PostForm" accepte maintenant 2 paramètres pour donner un nom au formulaire et pour définir quelques options. Les deux paramètres seront transmis 
au parent. On attribue un nom au "fieldset". Ces options seront transmises à partir du "FormElementManager" lorsque le "PostField" est créé. Pour que cela fonctionne, on 
devra également faire la même chose dans le champ.
*/
namespace Blog\Form;

use Zend\Form\Form;

class PostForm extends Form
{
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->add(array(
            'name' => 'post-fieldset',			
            'type' => 'Blog\Form\PostFieldset',
            'options' => array(
                'use_as_base_fieldset' => true
            )
        ));

        $this->add(array(
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => array(
                'value' => 'Ajouter'
            )
        ));
    }
}