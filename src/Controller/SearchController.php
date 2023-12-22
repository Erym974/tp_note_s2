<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{

    /**
     * 
     * Search for a user
     * 
     */

    #[Route('/search', name: 'search')]
    public function index(Request $request, EntityManagerInterface $manager): Response
    {

        $query = $request->query->get('q');
        $results = $manager->getRepository(User::class)->search($query);

        return $this->render('search/index.html.twig', [
            'results' => $results,
        ]);
    }
}
