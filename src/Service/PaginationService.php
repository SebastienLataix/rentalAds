<?php

namespace App\Service;

use Twig\Environment;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RequestStack;

class PaginationService {
    private $entityClass;
    private $limit = 10;
    private $currentPage = 1;
    private $manager;
    private $twig;
    private $route;
    private $templatePath;

    // Dans un service, l'injection se fait via le constructeur
    // La requestStack est a utiliser quand on veut accéder à la request depuis un service => permet de connaitre la requete qui est fait à ce moment précis.
    // On défini la variable $templatePath dans le fichier services.yaml
    public function __construct(ObjectManager $manager, Environment $twig, RequestStack $request, $templatePath) 
    {
        $this->route = $request->getCurrentRequest()->attributes->get('_route');
        $this->manager = $manager;
        $this->twig = $twig;
        $this->templatePath = $templatePath;
    }

    public function setRoute($route){
        $this->route = $route;
        return $this;
    }

    public function getRoute(){
        return $this->route;
    }

    public function display(){
        // $this->twig->display('admin/partials/pagination.html.twig', [ // on utilise maintenant des templates et non plus une déclaration en dur du template. => services.yaml
        $this->twig->display($this->templatePath, [
            'page' => $this->currentPage,
            'pages' => $this->getPages(),
            'route' => $this->route
        ]);
    }

    public function setTemplatePath($templatePath){
        $this->templatePath = $templatePath;
        return $this;
    }

    public function getTemplatePath(){
        return $this->templatePath;
    }

    public function getPages(){
        // Une exeption pour l'exemple
        if (empty($this->entityClass)){
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer ! utilisez la méthode setEntityClass() de votre objet PaginationService !");
        }
        // 1) Connaitre le total des enregistrements de la table
        $repo = $this->manager->getRepository($this->entityClass);
        $total = count($repo->findAll());
        // 2) Faire la division, l'arrondi et le renvoyer
        $pages = ceil($total / $this->limit);

        return $pages;
    }

    public function getData(){
        // Une exeption pour l'exemple
        if (empty($this->entityClass)){
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer ! utilisez la méthode setEntityClass() de votre objet PaginationService !");
        }

        // 1) Calcule de l'offset
        $offset = $this->currentPage * $this->limit - $this->limit;
        // 2) demander au repository de trouver les élements
        $repo = $this->manager->getRepository($this->entityClass);
        $data = $repo->findBy([], [] , $this->limit, $offset);
        // 3) Renvoyer les éléments en question
        return $data;
    }

    public function setPage($page){
        $this->currentPage = $page;
        return $this;
    }

    public function getPage(){
        return $this->currentPage;
    }

    public function setLimit($limit){
        $this->limit = $limit;
        return $this;
    }

    public function getLimit(){
        return $this->limit;
    }

    public function setEntityClass($entityClass){
        $this->entityClass = $entityClass;
        return $this;
    }

    public function getEntityClass(){
        return $this->entityClass;
    }

}