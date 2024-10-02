<?php
/*
https://framework.zend.com/manual/2.3/en/in-depth-guide/zend-form-zend-form-fieldset.html

ARRIVE A Displaying the form
*/
return array(
	/*
	Pour faire des requêtes sur une base de données à l'aide de Zend\Db\Sql, on doit diposer d'une connexion à la base de données.
	Cette connexion est servie via n'importe quel classe implémentant Zend\Db\Adapter\AdapterInterface. La façon la plus pratique 
	de créer une telle classe consiste à utiliser Zend\Db\Adapter\AdapterInterface qui écoute les clés de configuration de la base 
	de données. On va créer les entrées de configuration requises et modifier donc le "module.config.php" en ajoutant une nouvelle clé de 
	niveau supérieur "db". 	
	*/
    'db' => array(
        'driver'         => 'Pdo',
        'username'       => 'c0mcdcoll',  
        'password'       => '8329KUTCVDNw',  
        'dsn'            => 'mysql:dbname=c0mcdcollection;host=localhost',
        'driver_options' => array(
            \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        )
    ),	
	/*
	Après avoir introduit PostMapperInterface en tant que dépendance pour le PostService, on ne peut plus définir ce service comme "invocable" car il a une dépendance.
	On doit donc créer une "factory" pour le service, on va créez une "factory" de la même manière que pour le ListController. On va modifiez d'abord la configuration 
	d'une entrée "invocable" en une entrée de "factory" et affectez la classe de "factory" appropriée.
	*/
	'service_manager' => array(
        'factories' => array(
            'Blog\Mapper\PostMapperInterface'   => 'Blog\Factory\ZendDbSqlMapperFactory',
			/*
			Chaque fois qu'on a un paramètre requis, on doit écrire une "factory" pour la classe.On va créer une "factory" pour notre implémentation de mappeur. On peut maintenant enregistrer 
			l'implémentation de mappeur en tant que service. Si vous vous souvenez du chapitre précédent, on appele le service "Blog\Mapper\PostMapperInterface" pour obtenir une 
			implémentation de mappeur. On va modifiez la configuration pour que cette clé appelle la classe d'usine nouvellement appelée, on obtiendra un avertissement.
			*/
            'Blog\Service\PostServiceInterface' => 'Blog\Factory\PostServiceFactory',
            /*
			La prochaine chose qu'on doit faire est d'utiliser l'adaptateur "ServiceFactory" qui est une entrée 	
			ServiceManager. Ce nouveau service "Zend\Db\Adapter\Adapter" restituera désormais toujours une instance 
			en cours d'exécution de "Zend\Db\Adapter\AdapterInterface" en fonction du pilote qu'on affecte. Avec 
			l'adaptateur en palce, on peut exécuter des requêtes sur la base de données, la construction des requêtes 
			est mieux effectuées via les fonctionnalités "QueryBuilder".
			*/
			'Zend\Db\Adapter\Adapter'           => 'Zend\Db\Adapter\AdapterServiceFactory'
        )
    ),
	'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
	'controllers'  => array(
        'factories' => array(
            'Blog\Controller\List' => 'Blog\Factory\ListControllerFactory',
			/*
			Création du "WriteController"
			*****************************
			On a besoin d'un nouveau contrôleur qui est censé avoir 2 dépendances : une dépendance qui est le "PostService" qui est 
			également utilisé dans le "ListController" et l'autre dépendance est le "PostForm" qui est nouveau. Etant donné que le 
			"PostForm" est une dépendance dont "ListController" n'a pas besoin pour afficher les données de blog, on va créer un nouveau contrôleur 
			pour séparer les choses correctement. On va d'abord enregistrer une "factory" de contrôleurs dans la configuration.
			*/
			'Blog\Controller\Write' => 'Blog\Factory\WriteControllerFactory',
			'Blog\Controller\Delete' => 'Blog\Factory\DeleteControllerFactory',
        )
    ),
    // Cette ligne ouvre la configuration de RouteManager
	
	/*
	On a une configuration solide pour le module : tout ce qu'on fait, c'est afficher toutes les entrées du blog sur seule page. On peut, sur le routeur, créer 
	d'autres itinéraires pour afficher un seul blog, ajouter de nouveaux blogs à l'application et modifier et supprimer des blogs existants.
	
	Différents types de routes
	**************************
	Les types de routes les plus importantes 
	****************************************
	Le premier type de route commun est la route littérale : elle correspond à une chaîne spécifique. 
	
	Zend\Mvc\Router\Http\Segment
	****************************
	Le deuxième type d'itinéraire le plus couramment utilisé est le "segment-route" : un itinéraire segmenté est utilisé chaque fois que l'url est censée contenir 
	des paramètres variables, qui sont assez souvent utilisés dans l'application. La configuration d'un "segment-route" demande un peu plus d'efforts mais n'est pas difficile à comprendre.
	Les tâches qu'on a à faire sont similaires, il faut définir le type d'itinéraire et s'assurer de le faire en segment. Ensuite, on doit définir l'itinéraire et y ajouter 
	des paramètres. Ensuite, on définit les valeurs par défaut à utiliser, la seule chose qui diffère, c'est qu'on peut également attribuer des valeurs par défaut aux paramètres.
	La nouvelle partie utilisée sur les routes de type "segment" consiste à définir des contraintes qui sont utilisées pour indiquer au routeur quelles règles sont données pour les paramètres.
	Un paramètre "id" ne peut être que de type entier et ne peux contenir que 4 chiffres exactement. Sous contraintes, on a un autre tableau qui contient des règles d'expression réguliière pour chaque 
	paramètre de l'itinéraire. 
	
	Différents concepts de routage 
	******************************
	En pensant à l'ensemble de l'application, il y a beaucoup de route à faire correspondre, lors de l'écriture de ces itinéraires, on a 2 options : une option consiste à passer moins de temps à écrire 
	des itinéraires qui, à leur tour, sont un peu lents à correspondre, une autre option consiste à écrire des itinéraires très explicites qui correspondent un peu plus rapidement mais nécessitent plus de travail 
	à définir.
	
	Itinéraire générique 
	********************
	C'est celui qui correspond à de nombreuses URL, on définit le contrôleur, l'action et tous les paramètres au sein d'une seule route. Le gros avantage de cette approche est l'immense gain de temps lors du 
	développement de l'application. L'inconvénient est que l'appariement d'un tel itinéraire peut prendre un peu plus de temps car de nombreuses variables doivent être vérifiées. Tant qu'on en fait pas trop, c'est
	un concept viable. Dans cette configuration, la partie "route" contient 2 paramètres facultatifs : contrôleur et action. Le paramètre d'action est facultatif uniquement lorsque le paramètre de contrôleur est présent.
	Dans la section des valeurs par défaut, le "__NAMESPACE__" sera utilisé pour concaténer avec le paramètre du contrôleur est "news", le contrôleur à appeler depuis le routeur sera "Application\Controller\archive". 
	La section par défaut de nouveau simple : les 2 paramètres, contrôleur et action, doivent seulement suivre les convetions données par les normes PHP, ils doivent commencer par une lettre de a à z, en majuscule ou minusucle 
	et après cette première lettre, il peut y avoir une quantite ou presque infinie de lettres, chiffres, traites de soulignements ou tirets. Le gros inconvénient de cette approche est non seulement que la correspondance avec 
	cet itinéraire est un peu plus lente, mais il n'y a pas de vérification des erreurs en cours : si il n'existe pas de contrôleur, ni d'action, la route correspondra toujours mais une exception sera levée car le routeur ne pourra 
	pas trouvers les ressoureces demandées et on recevra une réponse "404".
	
	Routes explicites utilisant "child_routes"
	*****************************************
	Le routage explicite se fait en définissant tous les itinéraires possibles, pour cette méthode, il y a 2 options disponibles.
	
	Sans structure de configuration 
	*******************************
	La façon la plus facile à comprendre d'écrire des routes explicites est d'écrire de nombreuses routes de niveau supérieur. Tous les itinéraires ont un nom explicite et il y a beaucoup de répétitions. On doit redéfinir le contrôleur par défaut à utiliser à chaque 
	fois et on n'a pas vraiment de structure de configuration. 
	
	Utiliser "child_routes" pour plus de structure 
	**********************************************
	Une autre option pour définir des itinéraires explicites consiste à utiliser "child_routes", les itinéraires enfant héritent de toutes les options deleurs parents respectifs. Lorsque le contrôleur ne change pas, on n'a pas besoin de le redéfinir. On a une nouvelle entrée 
	*/
    'router' => array(
        /*
		On a un nouvelle entrée de configuration "may_termnate", propriété qui définit que la route parent peut être mise en correspondance seule, sans que les routes enfants ne doivent également être mises en correspondance seule. On définit de nouveaux itinéraires qui seront 
		ajoutés à l'itinéraire parent, il n'y a pas vraiment de différence de configuration entre les routes qu'on définit comme route enfant et les routes qui sont de niveaux supérieur de la configuration. La seule chose qui peut tomber est la redéfinition des valeurs par défaut 
		partagées. Le gros avantage est qu'on a avec ce type de configuration, la définition explicite et donc on ne rencontrera jamais de problèmes de contrôleurs inexistants comme on le ferais avec des routes génériques. Le deuxième avantage serait que ce type de routage est un peu 
		plus rapide que les itinéraires génériques et le dernier avantage serait qu'on peut facilement voir toutes les URL possibles commençant par "/news". Cela tombe dans la catégorie des préférences personnelles, sans oublier que le débogage des routes explicites est beaucoup plus facile 
		que le débogage des routes génériques. On a mis en place une nouvelle route qu'on utilise pour afficher une seule entrée de blog. On a assigné un paramètre "id" qui doit être un chiffre positif excluant 0. Les entrées de base de données commencent généralement par un 0 lorsqu'il s'agit 
		de clés d'indentification primaires e donc les contraintes d'expression régulière pour les champs "id" semblent un peu plus compliquées. On dit au routeur que l'id de paramètre doit commencer par un entier compris entre 1 et 9, c'est la partie [1-9] et après cela, 0 ou plusieurs 
		chiffres peuvent suivre, c'est la paritie "\d*. La route appelera le même contrôleur que la route parent mais elle appellera à la place "detailAction()". C'est du au fait que le contrôleur essaie d'accéder à détailAction() qui n'existe pas encore.  
		*/
		'routes' => array(
            'blog' => array(
                'type' => 'literal',
                'options' => array(
                    'route'    => '/blog',
                    'defaults' => array(
                        'controller' => 'Blog\Controller\List',
                        'action'     => 'index',
                    )
                ),
                'may_terminate' => true,
                 'child_routes'  => array(
                    'detail' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route'    => '/:id',
                            'defaults' => array(
                                'action' => 'detail'
                            ),
                            'constraints' => array(
                                'id' => '\d+'
                            )
                        )
                    ),
					/* Ajout de sujets */
                    'add' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route'    => '/add',
                            'defaults' => array(
                                'controller' => 'Blog\Controller\Write',
                                'action'     => 'add'
                            )
                        )
                    ),
					/* Modification de sujets */
					'edit' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route'    => '/edit/:id',
                            'defaults' => array(
                                'controller' => 'Blog\Controller\Write',
                                'action'     => 'edit'
                            ),
                            'constraints' => array(
                                'id' => '\d+'
                            )
                        )
                    ),
					/* Suppresion d'un sujet */
					'delete' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route'    => '/delete/:id',
                            'defaults' => array(
                                'controller' => 'Blog\Controller\Delete',
                                'action'     => 'delete'
                            ),
                            'constraints' => array(
                                'id' => '\d+'
                            )
                        )
                    ),					
                )
            )
        )
    )
 );