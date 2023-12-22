<?php

namespace App\Controller;

use App\Entity\Invitation;
use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use App\Service\FormService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{

    #[Route(path: '/profile/{user}', name: 'profile.index')]
    public function index(User $user, EntityManagerInterface $manager, Request $request, PaginatorInterface $paginator, FormService $formService)
    {
        /** @var User */
        $me = $this->getUser();

        /** Get posts and paginate */
        $posts = $manager->getRepository(Post::class)->getUserPost($user, $me);
        $page = $request->query->get('page', 1);
        $posts = $paginator->paginate($posts, $page, 10);

        /** Check if user is friend or check invite */
        $isFriend = $me->getFriends()->contains($user);
        if(!$isFriend){
            $invitation = $manager->getRepository(Invitation::class)->findOneBy([
                'emitter' => $me,
                'receiver' => $user,
            ]) ?? $manager->getRepository(Invitation::class)->findOneBy([
                'receiver' => $me,
                'emitter' => $user,
            ]);
        }

        /** Edit Post form */
        if($request->query->get('edit')) {
            $post = $manager->getRepository(Post::class)->find($request->query->get('edit'));
            if($post && ($post->getAuthor() == $me || $this->isGranted('ROLE_MODERATOR'))) {
                $editForm = $this->createForm(PostType::class, $post);
                $editForm->handleRequest($request);
                $result = $formService->handleEditPost($editForm, $post);
                if($result) {
                    $referer = $request->headers->get('referer');
                    if($referer) {
                        if(strpos($referer, 'edit')) $referer = substr($referer, 0, strpos($referer, 'edit') - 1);
                        return $this->redirect($referer);
                    }
                    return $this->redirectToRoute('profile.index', [ 'user' => $user->getId(), 'page' => $request->query->get('page', 1)  ]);
                }
            }
        }

        
        /** New Comment Form */
        $commentResult = $formService->handleNewComment();
        if($commentResult) return $this->redirectToRoute('profile.index', [ 'user' => $user->getId(), 'page' => $request->query->get('page', 1)  ]);

        return $this->render('profile/index.html.twig', [
            'posts' => $posts,
            'profiled' => $user,
            'isFriend' => $isFriend,
            'editForm' => isset($editForm) ? $editForm->createView() : null,
            'invitation' => $invitation ?? null
        ]);
    }

}