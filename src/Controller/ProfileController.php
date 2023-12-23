<?php

namespace App\Controller;

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
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
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

        /** New Post form */
        if($me === $user){
            $newPostResponse = $formService->handleNewPost();
            if(isset($newPostResponse['response'])) return $newPostResponse['response'];
            $newPost = $newPostResponse['form'] ?? null;
        }

        /** Edit Post form */
        $editResponse = $formService->handleEditPost();
        if(isset($editResponse['response'])) return $editResponse['response'];
        $editForm = $editResponse['form'] ?? null;

        /** Report Form */
        $reportResponse = $formService->handleReportPost();
        if(isset($reportResponse['response'])) return $reportResponse['response'];
        $reportForm = $reportResponse['form'] ?? null;
        
        /** New Comment Form */
        $commentResult = $formService->handleNewComment();
        if($commentResult) return $this->redirectToRoute('profile.index', [ 'user' => $user->getId(), 'page' => $request->query->get('page', 1)  ]);

        return $this->render('profile/index.html.twig', [
            'posts' => $posts,
            'profiled' => $user,
            'isFriend' => $isFriend,
            'invitation' => $invitation ?? null,
            'newPost' => isset($newPost) ? $newPost->createView() : null,
            'editForm' => $editForm ? $editForm->createView() : null,
            'reportForm' => $reportForm ? $reportForm->createView() : null,
        ]);
    }

}