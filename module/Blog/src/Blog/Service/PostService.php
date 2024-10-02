<?php
/*
Refactoring du PostService 
**************************
Quand on a défini comment le mappeur doit agir, on peut l'utiliser dans le PostService. On va vider classe et supprimer tout le contenu actuel. 
Ensuite, on implémente les fonctions définies par PostServiceInterface, cette interface n'est pas implémentée dans le PostService mais est plutôt utilisée 
comme dépendance. Une dépendance requise, on doit créer un __construct () qui prend en paramètre toute implémentation de cette interface. On doit également créer 
une variable protégée dans laquelle on stocke le paramètre. On maintenant besoin d'une implémentation de PostMapperInterface pour que le PostService fonctionne. 
Puisqu'il n'en existe pas encore, on ne peut pas faire fonctionner notre application et on aura une erreur. Ce PostService aura toujours un mappeur passé en argument. 
Donc, dans les fonctions find * (), on peut supposer qu'il est là. Le PostMapperInterface définit une fonction find ($ id) et findAll (). Il faut les utiliser dans
les fonctions de service. On utilise le PostMapper pour avoir accès aux données, la façon dont cela se produit n'est plus l'affaire de PostService mais il sait quelles 
données il recevra.
*/
namespace Blog\Service;

use Blog\Mapper\PostMapperInterface;
use Blog\Model\PostInterface;

class PostService implements PostServiceInterface
{
    /**
     * @var \Blog\Mapper\PostMapperInterface
     */
    protected $postMapper;

    /**
     * @param PostMapperInterface $postMapper
     */
    public function __construct(PostMapperInterface $postMapper)
    {
        $this->postMapper = $postMapper;
    }

    /**
     * {@inheritDoc}
     */
    public function findAllPosts()
    {
        return $this->postMapper->findAll();
    }

    /**
     * {@inheritDoc}
     */
    public function findPost($id)
    {
        return $this->postMapper->find($id);
    }

    /**
     * {@inheritDoc}
     */
    public function savePost(PostInterface $post)
    {
        return $this->postMapper->save($post);
    }

    /**
     * {@inheritDoc}
     */
    public function deletePost(PostInterface $post)
    {
        return $this->postMapper->delete($post);
    }
}