<?php
// Le module créé peut être détecté par ZF2 ModuleManager, on ajoute ce module à l'application 
// il faut donc ajouter une entrée pour le module "Blog" au tableau des modules dans ce fichier de configuration.
return array(
    'modules' => array(
        'Application',
        'Album',  
		'Blog'
    ),	
    'module_listener_options' => array(
        'config_glob_paths'    => array(
            'config/autoload/{,*.}{global,local}.php',
        ),
        'module_paths' => array(
            './module',
            './vendor',
        ),
    ),
);
