<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Invitation;
use App\Entity\Post;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\PostType;
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

        
        /** New Post Form */
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        $result = $formService->handleNewPost($form, $post);
        if($result) return $this->redirectToRoute('home.index', [ 'page' => $request->query->get('page', 1)  ]);

        /** New Comment Form */
        $commentResult = $formService->handleNewComment();
        if($commentResult) return $this->redirectToRoute('home.index', [ 'page' => $request->query->get('page', 1)  ]);

        /** Edit Post form */
        if($request->query->get('edit')) {
            $post = $manager->getRepository(Post::class)->find($request->query->get('edit'));
            if($post && ($post->getAuthor() == $user || $this->isGranted('ROLE_MODERATOR'))) {
                $editForm = $this->createForm(PostType::class, $post);
                $editForm->handleRequest($request);
                $result = $formService->handleEditPost($editForm, $post);
                if($result) {
                    $referer = $request->headers->get('referer');
                    if($referer) {
                        if(strpos($referer, 'edit')) if(strpos($referer, 'edit')) $referer = substr($referer, 0, strpos($referer, 'edit') - 1);
                        return $this->redirect($referer);
                    }
                    return $this->redirectToRoute('profile.index', [ 'user' => $user->getId(), 'page' => $request->query->get('page', 1)  ]);
                }
            }
        }

        /** Get Posts and paginate */
        $posts = $manager->getRepository(Post::class)->getFeed($user);
        $page = $request->query->get('page', 1);
        $posts = $paginator->paginate($posts, $page, 10);

        $invitations = $manager->getRepository(Invitation::class)->getInvitationsOfUser($user);

        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
            'posts' => $posts,
            'editForm' => isset($editForm) ? $editForm->createView() : null,
            'invitations' => $invitations
        ]);
    }

}