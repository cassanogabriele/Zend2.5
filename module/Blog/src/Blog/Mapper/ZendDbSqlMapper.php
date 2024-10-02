<?php
/*
Écrire l'implémentation du mappeur
**********************************
L'implémentation de mappeur résidera dans le même espace de noms que son interface. On va créer une classe "ZendDbSqlMapper" et implémentez PostMapperInterface.
Pour que "Zend\Db\Sql" fonctionne, on aura besoin d'une implémentation fonctionnelle de AdapterInterface. C'est une exigence et ce sera donc injecté en utilisant 
l'injection de constructeur. Il faut créer une fonction __construct () qui accepte un AdapterInterface comme paramètre et stockez-le dans la classe.
On va modifier la fonction "findAll()" pour renvoyer tous les "posts" de la table de la base de données. Le code est assez simple mais une actualisation de l'application 
affiche une autre erreur. On ne retourne pas la variable $result pour l'instant et on fait un vidage pour voir ce qu'on obtient, il faut modifier la fonction findAll() et 
effectuer un vidage des données de la variable $result. On ne récupère aucune donnée, on fait un vidage de l'objet $result qui semble ne contenir aucune donnée. Cet objet 
ne dispose pas d'informations quand on essaie d'y accéder. Pour utiliser les données dans l'objet $result, la meilleure approche serait de passer l'objet Result dans un 
objet ResultSet, tant que la requêt a réussi. Si on actualise la page, on voit le vidage d'un objet ResultSet qui a un propriété ["count": protected] => int (5), ce qui signifie
qu'on a 5 lignes dans la base de données. Une autre propriété très intéressante est ["returnType": protected] => string (11) "arrayobject". Cela indique que toutes les entrées de la base de données
seront retournées en tant qu'ArrayObject. Et c'est un petit problème car PostMapperInterface oblige à renvoyer un tableau d'objets PostInterface. Il existe pour une 
option très simple pour y parvenir. On as utilisé l'objet ResultSet par défaut. Il existe également un HydratingResultSet qui hydratera les données données dans un 
objet fourni. Cela signifie: si on demande à HydratingResultSet d'utiliser les données de la base de données pour créer des objets Posts, on va modifier le code. On a changé 2 ou 3 choses : au lieu d'un ResultSet normal, 
on utilise le HydratingResultSet, cet objet nécessite 2 paramètres, le second étant l'objet dans lequel s'hydrater et le premier étant l'hydrateur qui sera utilisé. 
Un hydrateur est un objet qui change toute sorte de données d'un format à un autre. Le InputFormat qu'on a est un ArrayObject mais on veut des post-modèles. L'hydrateur ClassMethods s'en chargera en utilisant les fonctions 
setter et getter de notre Post-modèle. Au lieu de vider la variable $ result, on retourne  directement le HydratingResultSet initialisé afin qu'on puisse accéder aux données stockées dans. 
Dans le cas où on obtine quelque chose d'autre retourné qui n'est pas une instance de ResultInterface, on retourne un tableau vide. En actualisant la page, on voit désormais tous les articles de blog répertoriés sur la page.

Refactoring des dépendances cachées
***********************************
Il y a une petite chose qu'on a faite qui n'est pas une bonne pratique : on utilise à la fois un hydrateur et un objet à l'intérieur. Maintenant que le mappeur nécessite plus de paramètres, on doit mettre à jour 
ZendDbSqlMapperFactory et injecter ces paramètres. Avec cela en place, on peut actualiser à nouveau l'application et on verrez à nouveau la liste des articles de blog. 
Le mappeur a maintenant une très bonne architecture et plus de dépendances cachées.

Finir le mappeur
****************
On terrmie  le mappeur en écrivant une implémentation pour la méthode find().
*/
namespace Blog\Mapper;

use Blog\Model\PostInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Delete;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Update;
use Zend\Stdlib\Hydrator\HydratorInterface;

class ZendDbSqlMapper implements PostMapperInterface
{
    /**
     * @var \Zend\Db\Adapter\AdapterInterface
     */
    protected $dbAdapter;

    protected $hydrator;

    protected $postPrototype;

    /**
     * @param AdapterInterface  $dbAdapter
     * @param HydratorInterface $hydrator
     * @param PostInterface    $postPrototype
     */
    public function __construct(
        AdapterInterface $dbAdapter,
        HydratorInterface $hydrator,
        PostInterface $postPrototype
    ) {
        $this->dbAdapter      = $dbAdapter;
        $this->hydrator       = $hydrator;
        $this->postPrototype  = $postPrototype;
    }

    /**
     * {@inheritDoc}
     */
    public function find($id)
    {
        $sql    = new Sql($this->dbAdapter);
        $select = $sql->select('posts');
        $select->where(array('id = ?' => $id));
        $stmt   = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();

        if ($result instanceof ResultInterface && $result->isQueryResult() && $result->getAffectedRows()) {
            return $this->hydrator->hydrate($result->current(), $this->postPrototype);
        }

        throw new \InvalidArgumentException("Blog avec ID donné: {$id} pas trouvé.");
    }

    /**
     * {@inheritDoc}
     */
    public function findAll()
    {
        $sql    = new Sql($this->dbAdapter);
        $select = $sql->select('posts');
        $stmt   = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();

        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet = new HydratingResultSet($this->hydrator, $this->postPrototype);

            return $resultSet->initialize($result);
        }

        return array();
    }

    /**
     * {@inheritDoc}
     */
    public function save(PostInterface $postObject)
    {
        $postData = $this->hydrator->extract($postObject);
        unset($postData['id']); // Neither Insert nor Update needs the ID in the array

        if ($postObject->getId()) {
            // ID present, it's an Update
            $action = new Update('posts');
            $action->set($postData);
            $action->where(array('id = ?' => $postObject->getId()));
        } else {
            // ID NOT present, it's an Insert
            $action = new Insert('post');
            $action->values($postData);
        }

        $sql    = new Sql($this->dbAdapter);
        $stmt   = $sql->prepareStatementForSqlObject($action);
        $result = $stmt->execute();

        if ($result instanceof ResultInterface) {
           if ($newId = $result->getGeneratedValue()) {
                // When a value has been generated, set it on the object
                $postObject->setId($newId);
            }

            return $postObject;
        }

        throw new \Exception("Database error");
    }

    /**
     * {@inheritDoc}
     */
    public function delete(PostInterface $postObject)
    {
        $action = new Delete('posts');
        $action->where(array('id = ?' => $postObject->getId()));

        $sql    = new Sql($this->dbAdapter);
        $stmt   = $sql->prepareStatementForSqlObject($action);
        $result = $stmt->execute();

        return (bool)$result->getAffectedRows();
    }
}