<?php
/*
Maintenant qu'on a le "PostForm" dans le "WriteController", il est temps de passer ce formulaire à la vue et de la rendre à l'aide 
des "View Helpers" fournis depuis le composant Zend\Form. Il faut d'abord modifier le contrôleur afin que le formulaire soit transmis 
à la vue.

Logique de contrôleur pour pratiquement tous les formulaires
************************************************************
L'écriture d'un contrôleur qui gère un flux de travail est simpole et identique pour chaque formulaire qu'on aura dans l'application. On souhaite 
vérifier si la demande en cours est une demande "POST", donc si le formulaire est envoyé, si c'est le cas, on souhaite stocker les données "POST" dans le 
formulaire et vérifier si le formulaire passe la validation. Si il passe la validation, on souhaite transmettre les données du formulaire au service pour les 
stocker et rediriger l'utilisateur vers la page de détail des données saisies ou vers une page de présentation. Dans tous les autres cas, on souhaite que le 
formulaire soit affiché, parfois avec des messages d'erreur donnés. On enregistre tout d'abord la demande actuelle dans une variable locale, ensuite, on vérifie 
si la demande actuelle est une demande "POST" et si c'est le cas, on stocke les données "POST" des demandes dans le formulaire. Si le formulaire est valide, on essaie 
d'enregistrer les données du formulaire via le service, puis on redirige l'utilisateur vers le blog. Si il y a une erreur, on affiche le formulaire.
*/
namespace Blog\Controller;

use Blog\Service\PostServiceInterface;
use Zend\Form\FormInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class WriteController extends AbstractActionController
{
    protected $postService;

    protected $postForm;

    public function __construct(
        PostServiceInterface $postService,
        FormInterface $postForm
    ) {
        $this->postService = $postService;
        $this->postForm    = $postForm;
    }

    public function addAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $this->postForm->setData($request->getPost());

            if ($this->postForm->isValid()) {
                try {
                    $this->postService->savePost($this->postForm->getData());
                    
					return $this->redirect()->toRoute('blog');
                } catch (\Exception $e) {
                    throw new \Exception("Pas de commentaire à enregistrer");
                }
            }
        }

        return new ViewModel(array(
            'form' => $this->postForm
        ));
    }
	
	/*
	Liaison d'objets à des formulaires
	**********************************
	La seule différence fondamentale entre un formulaire d'insertion est un formulaire d'édition est qu'il existe déjà des données prédéfinies.
	On doit trouver un moyen d'obtenir les données de la base de données dans le formulaire. Zend\Form fournit un moyen très pratique de le faire, 
	c'est la liasion des données. On doit fournir un formulaire de modification et obtenir un objet d'intérêt de service et le lier lier au formulaire.
	*/
	public function editAction()
    {
        $request = $this->getRequest();
        $post    = $this->postService->findPost($this->params('id'));

        $this->postForm->bind($post);

        if ($request->isPost()) {
            $this->postForm->setData($request->getPost());

            if ($this->postForm->isValid()) {
                try {
                    $this->postService->savePost($post);

                    return $this->redirect()->toRoute('blog');
                } catch (\Exception $e) {
                    die($e->getMessage());
                    // Some DB Error happened, log it and let the user know
                }
            }
        }

        return new ViewModel(array(
            'form' => $this->postForm
        ));
    }
}