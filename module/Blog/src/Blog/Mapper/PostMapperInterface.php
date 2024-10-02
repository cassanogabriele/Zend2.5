<?php
/*
Création d'une nouvelle interface 
*********************************
On doit pouvoir : 

- trouver un seul article de blog 
- trouver tous les articles de blog 
- insérer un nouveau billet de blog 
- mettre à jour les articles de blog existants 
- supprimer les articles de blog existants 

insert() et update() écrivent dans la base de données, il serait bien de n'avoir qu'une seule fonction "save()"
qui appelle la fonction appropriée en interne.

On définit 2 fonctions différents : la fonction "find()" qui renvoie un seul object implémentant la "PostInterface" 
et la fonction "findaAll()" qui retourne un tableau d'objets implémentant la "PostInterface". Les définitions d'une 
éventuelle fonctionnalité de sauvegarde ou de suppression ne seoront pas encore ajoutées à l'interface car on ne se penche 
que sur ce côté en lecture seule pour l'instant.
*/
namespace Blog\Mapper;

use Blog\Model\PostInterface;

interface PostMapperInterface
{
    /**
     * @param int|string $id
     * @return PostInterface
     * @throws \InvalidArgumentException
     */
    public function find($id);

    /**
     * @return array|PostInterface[]
     */
    public function findAll();

    /**
     * @param PostInterface $postObject
     *
     * @param PostInterface $postObject
     * @return PostInterface
     * @throws \Exception
     */
    public function save(PostInterface $postObject);

    /**
     * @param PostInterface $postObject
     *
     * @return bool
     * @throws \Exception
     */
    public function delete(PostInterface $postObject);
}