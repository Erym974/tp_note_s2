<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Invitation;
use App\Entity\Post;
use App\Entity\Report;
use App\Entity\User;
use App\Form\PostType;
use App\Form\ReportType;
use App\Service\FormService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class HomeController extends AbstractController
{

    #[Route(path: '/', name: 'home.index')]
    public function home(Request $request, FormService $formService, EntityManagerInterface $manager, PaginatorInterface $paginator): Response
    {

        /** @var User */
        $user = $this->getUser();

        /** New Post form */
        $newPostResponse = $formService->handleNewPost();
        if(isset($newPostResponse['response'])) return $newPostResponse['response'];
        $newPost = $newPostResponse['form'] ?? null;

        /** New Comment Form */
        $commentResult = $formService->handleNewComment();
        if ($commentResult) return $this->redirectToRoute('home.index', ['page' => $request->query->get('page', 1)]);

        /** Edit Post form */
        $editResponse = $formService->handleEditPost();
        if(isset($editResponse['response'])) return $editResponse['response'];
        $editForm = $editResponse['form'] ?? null;

        /** Report Form */
        $reportResponse = $formService->handleReportPost();
        if(isset($reportResponse['response'])) return $reportResponse['response'];
        $reportForm = $reportResponse['form'] ?? null;

        /** Get Posts and paginate */
        $posts = $manager->getRepository(Post::class)->getFeed($user);
        $page = $request->query->get('page', 1);
        $posts = $paginator->paginate($posts, $page, 10);

        $invitations = $manager->getRepository(Invitation::class)->getInvitationsOfUser($user);

        return $this->render('home/index.html.twig', [
            'posts' => $posts,
            'invitations' => $invitations,
            'newPost' => $newPost->createView(),
            'editForm' => $editForm ? $editForm->createView() : null,
            'reportForm' => $reportForm ? $reportForm->createView() : null,
        ]);
    }
}
