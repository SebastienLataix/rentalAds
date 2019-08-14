<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Repository\BookingRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\AdminBookingType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use App\Service\PaginationService;

class AdminBookingController extends AbstractController
{
    /**
     * @Route("/admin/bookings/{page<\d+>?1}", name="admin_bookings_index")
     */
    public function index(BookingRepository $repo, $page, PaginationService $pagination)
    {
        // $limit = 10;
        // $start = $page * $limit - $limit;
        // $total = count($repo->findAll());
        // $pages = ceil($total/$limit);
        // // On remplace tout ça en utilisant le service PaginationService

        $pagination->setEntityClass(Booking::class)
                ->setPage($page)
                // ->setRoute('admin_bookings_index') // pas besoin car def directement dans paginationService.php avec la requeste de type RequestStack !!!
                ->setLimit(11);
        
        return $this->render('admin/booking/index.html.twig', [
            // 'bookings' => $pagination->getData(),
            // 'pages' => $pagination->getPages(),
            // 'page' => $page
            // On peut passer directement la variable pagination en adaptant le twig à partri de cette unique variable
            'pagination' => $pagination
        ]);
    }

    /**
     * Permet d'éditer une réservation
     * 
     * @Route("/admin/bookings/{id}/edit", name="admin_booking_edit")
     *
     * @return Response
     */
    public function edit(Booking $booking, Request $request, ObjectManager $manager)
    {
        $form = $this->createForm(AdminBookingType::class, $booking);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            // On peut redéfinir le montant
            //$booking->setAmount($booking->getAd()->getPrice()*$booking->getDuration());
            // On peut aussi remettre le montant à 0, qui est considéré comme "empty" et donc recalculé par la function prePersist() de l'entité booking en rajoutant aux commentaires de cette fonction la déclaration => @ORM\preUpdate
            $booking->setAmount(0);
            
            $manager->persist($booking);
            $manager->flush();

            $this->addFlash(
                'success',
                "La réservation n° <strong>{$booking->getId()}</strong> a bien été modifiée."
            );

            return $this->redirectToRoute('admin_bookings_index');
        }

        return $this->render('admin/booking/edit.html.twig', [
            'form' => $form->createView(),
            'booking' => $booking
        ]);
    }

    /**
     * Permet de supprimer une réservation
     * 
     * @Route("/admin/bookings/{id}/delete", name="admin_booking_delete")
     *
     * @param Booking $booking
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(Booking $booking, ObjectManager $manager)
    { 
        $manager->remove($booking);
        $manager->flush();

        $this->addFlash(
            'success',
            "La réservation de <strong>{$booking->getBooker()->getFullName()}</strong> a bien été supprimée."
        );
         
        return $this->redirectToRoute('admin_bookings_index');
    }
}
