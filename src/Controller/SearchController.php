<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class SearchController extends AbstractController
{

    /**
     * 
     * Search for a user
     * 
     */

    #[Route('/search', name: 'search')]
    public function index(Request $request, EntityManagerInterface $manager, PaginatorInterface $paginator): Response
    {

        $query = $request->query->get('q');
        $results = $manager->getRepository(User::class)->search($query);

        $page = $request->query->get('page', 1);
        $results = $paginator->paginate($results, $page, 10);

        return $this->render('search/index.html.twig', [
            'results' => $results,
        ]);
    }
}
