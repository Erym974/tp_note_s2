<?php

namespace App\Controller;

use App\Entity\Invitation;
use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use App\Service\FormService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class HomeController extends AbstractController
{

    #[Route(path: '/', name: 'home.index')]
    public function home(Request $request, FormService $formService, EntityManagerInterface $manager): Response
    {

        /** @var User */
        $user = $this->getUser();

        $post = new Post();

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        $result = $formService->handleNewPost($form, $post);

        if($result) return $this->redirectToRoute('home.index');

        $posts = $manager->getRepository(Post::class)->getFeed($user);

        $invitations = $manager->getRepository(Invitation::class)->getInvitationsOfUser($user);

        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
            'posts' => $posts,
            'invitations' => $invitations
        ]);
    }

}