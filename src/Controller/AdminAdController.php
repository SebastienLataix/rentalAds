<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AnnonceType;
use App\Repository\AdRepository;
use App\Service\PaginationService;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdController extends AbstractController
{
    /**
     * @Route("/admin/ads/{page<\d+>?1}", name="admin_ads_index")
     * 
     * autre façon de déclarer le requirements <> et le ? indique que le paramètre est optionnel
     * PROBLEME: Ca fonctionne correctement sans le ? et pas avec alors que dans le tuto c'est l'inverse en laissant la valeur par defaut $page =1
     * Tout fonctionne par contre avec le ? pour indiquer que le parametre page est optionnel et qu'on indique la valeur par défaut avec ?1
     */
    public function index(AdRepository $repo, $page, PaginationService $pagination)
    {
        // // méthode find : permet de retrouver un enregistrement par son id
        // $ad = $repo->find(225);
        // // findOneBy : permet de retrouver un enregistrement même avec plusieurs critères
        // $ad = $repo->findOneBy([
        //     'title' => 'Aut reiciendis ad sint sit architecto iste.',
        //     'id' => 230
        // ]);
        // // findBy : permet de retrouver plusieurs enregistrements même avec plusieurs critères
        // // prend 4 arguments :
        // // - tableau des critères
        // // - tableau des ordres (ordonnancement)
        // // - Une limite (nombre d'enregistrements remontés)
        // // - Un offset (à partir de où je veux partir)
        // $ads = $repo->findBy([], [], 5, 0);      
        // dump($ads);

        // $limit = 10;
        // $start = $page * $limit - $limit;
        // $total = count($repo->findAll());
        // $pages = ceil($total/$limit); // ceil arrondi à l'entier supérieur
        // // On remplace tout ça en utilisant le service PaginationService

        $pagination->setEntityClass(Ad::class)
                ->setPage($page)
                // ->setRoute('admin_ads_index') // pas besoin car def directement dans paginationService.php avec la requeste de type RequestStack !!!
                ->setLimit(11);

        return $this->render('admin/ad/index.html.twig', [
            // 'ads' => $repo->findby([], [], $limit, $start),
            // 'pages' => $pages,
            // 'page' => $page
            // On peut passer directement la variable pagination en adaptant le twig à partri de cette unique variable
            'pagination' => $pagination
        ]);
    }

    /**
     * Permet d'afficher le formulaire
     * 
     * @Route("/admin/ads/{id}/edit", name="admin_ads_edit")
     *
     * @param Ad $ad
     * @return Response
     */
    public function edit(Ad $ad, Request $request, ObjectManager $manager)
    {
        $form = $this->createForm(AnnonceType::class, $ad);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été modifiée."
            );
        }

        return $this->render('admin/ad/edit.html.twig', [
            'ad' => $ad,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer une annonce
     * 
     * @Route("/admin/ads/{id}/delete", name="admin_ads_delete")
     *
     * @param Ad $ad
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(Ad $ad, ObjectManager $manager)
    {
        if(count($ad->getBookings()) > 0) {
            $this->addFlash(
                'warning',
                "L'annonce <strong>{$ad->getTitle()}</strong> possède des réservations et ne peut être supprimée."
            );
        } else {
            $manager->remove($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été supprimée."
            );
        }
            
        return $this->redirectToRoute('admin_ads_index');
    }
}