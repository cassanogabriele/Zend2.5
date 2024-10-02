<?php
namespace Album\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class AlbumTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll($paginated=false)
    {
        if ($paginated) {
			// Créer un nouvel objet "select" qui sélectionne toutes les données de la table "album"
			$select = new Select('album');
            // Créer un nouvel ensemble de résultats basé sur l'entité "Album"
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Album());
            // Créer un nouvel objet adaptateur de pagination
            $paginatorAdapter = new DbSelect(
                // L'objet "select" configuré
                $select,
                // L'adaptateur pour l'exécuter
                $this->tableGateway->getAdapter(),
                // Le résultat réglé pour s'hydrater
                $resultSetPrototype
            );
			 
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }
		 
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getAlbum($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
		
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
		
        return $row;
     }

    public function saveAlbum(Album $album)
    {
        $data = array(
            'artist' => $album->artist,
            'title'  => $album->title,
        );

        $id = (int) $album->id;
		
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getAlbum($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Album id does not exist');
            }
        }
    }

    public function deleteAlbum($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
}