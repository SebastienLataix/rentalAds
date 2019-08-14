<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ObjectManager;
use App\Service\StatsService;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function index(ObjectManager $manager, StatsService $statsService)
    {
        // $users = $statsService->getUsersCount();
        // $ads = $statsService->getAdsCount();
        // $bookings = $statsService->getBookingsCount();
        // $comments = $statsService->getCommentsCount();

        $stats = $statsService->getStats();

        //$bestAds = $statsService->getBestAds();
        $bestAds = $statsService->getAdsStats('DESC');
        $worstAds = $statsService->getAdsStats('ASC');

        return $this->render('admin/dashboard/index.html.twig', [
            // 'stats' => [
            //     'users' => $users,
            //     'ads' => $ads,
            //     'bookings' => $bookings,
            //     'comments' => $comments
            // ]
            // astuce PHP => la fonction compact ernvoi un tableau avec des clées-valeurs identiques, la ligne ci-dessous est donc identique aux lignes ci-dessus.
            //'stats' => compact('users','ads','bookings','comments'),
            // utilisation directement de la variable $stats
            'stats' => $stats,
            'bestAds' => $bestAds,
            'worstAds' => $worstAds
        ]);
    }
}
