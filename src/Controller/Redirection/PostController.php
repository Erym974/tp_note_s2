<?php

namespace App\Controller\Redirection;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class PostController extends AbstractController 
{
    
    /**
     * 
     *  Remove a post
     * 
     */

    #[Route(path: '/redirection/remove/{post}', name: 'redirection.posts.remove')]
    public function remove(Post $post, EntityManagerInterface $manager, Request $request): Response
    {

        /** @var User */
        $user = $this->getUser();
        
        if($post->getAuthor() == $user || $this->isGranted('ROLE_MODERATOR')) {
            $manager->remove($post);
            $manager->flush();
            $this->addFlash('success', 'You have removed your post.');
        } else {
            $this->addFlash('danger', 'You are not allowed to do this action.');
        }

        $referer = $request->headers->get('referer');
        if($referer) return $this->redirect($referer);

        return $this->redirectToRoute('profile.index', ['user' => $user->getId()]);

    }

}