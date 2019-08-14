<?php

namespace App\Service;

use Doctrine\Common\Persistence\ObjectManager;

class StatsService {
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    public function getUsersCount(){
        // En QDL, on ne s'intéresse pas aux tables, mais aux entités.
        // La méthode getResult() récupère les résultats sous forme d'objets Entité (ici des objets User).
        // La méthode getSingleScalarResult() permet d'ontenir le résultat sous la forme d'une variable scalaire simple.
        return $this->manager->createQuery('SELECT COUNT(u) FROM App\entity\User u')->getSingleScalarResult();
    }

    public function getAdsCount(){
        return $this->manager->createQuery('SELECT COUNT(a) FROM App\entity\Ad a')->getSingleScalarResult();
    }

    public function getBookingsCount(){
        return $this->manager->createQuery('SELECT COUNT(b) FROM App\entity\Booking b')->getSingleScalarResult();
    }

    public function getCommentsCount(){
        return $this->manager->createQuery('SELECT COUNT(c) FROM App\entity\comment c')->getSingleScalarResult();
    }

    public function getStats() {
        $users = $this->getUsersCount();
        $ads = $this->getAdsCount();
        $bookings = $this->getBookingsCount();
        $comments = $this->getCommentsCount();
        // 'stats' => [
            //     'users' => $users,
            //     'ads' => $ads,
            //     'bookings' => $bookings,
            //     'comments' => $comments
            // ]
            // astuce PHP => la fonction compact ernvoi un tableau avec des clées-valeurs identiques
        return compact('users','ads','bookings','comments');
    }

    /**
     * Permet de s'affranchir des deux fonction getBestAds() & getWorstads()
     *
     * @param [string] $direction
     * @return []
     */
    public function getAdsStats($direction){
        return $this->manager->createQuery(
            'SELECT AVG(c.rating) as note, a.title, a.id, u.firstName, u.lastName, u.picture
            FROM App\Entity\Comment c
            Join c.ad a
            Join a.author u
            GROUP BY a
            ORDER by note '.$direction
        )->setMaxResults(5)
        ->getResult();
    }

    public function getBestAds(){
        return $this->manager->createQuery(
            'SELECT AVG(c.rating) as note, a.title, a.id, u.firstName, u.lastName, u.picture
            FROM App\Entity\Comment c
            Join c.ad a
            Join a.author u
            GROUP BY a
            ORDER BY note DESC'
        )->setMaxResults(5)
        ->getResult();
    }

    public function getWorstads(){
        return $this->manager->createQuery(
            'SELECT AVG(c.rating) as note, a.title, a.id, u.firstName, u.lastName, u.picture
            FROM App\Entity\Comment c
            Join c.ad a
            Join a.author u
            GROUP BY a
            ORDER BY note ASC'
        )->setMaxResults(5)
        ->getResult();
    }

}