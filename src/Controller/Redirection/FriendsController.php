<?php

namespace App\Controller\Redirection;

use App\Entity\Invitation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class FriendsController extends AbstractController 
{
    
    /**
     * 
     * Remove a friend
     * 
     */

    #[Route(path: '/friends/remove/{user}', name: 'friends.remove')]
    public function remove(User $user, EntityManagerInterface $manager): Response
    {

        /** @var User */
        $me = $this->getUser();
        
        if($me->getFriends()->contains($user)) {
            $me->removeFriend($user);
            $user->removeFriend($me);
            $manager->persist($me);
            $manager->persist($user);
            $manager->flush();
            $this->addFlash('success', 'You have removed ' . $user->getUsername() . ' from your friends.');
        } else {
            $this->addFlash('danger', 'You are not allowed to do this action.');
        }

        return $this->redirectToRoute('profile.index', ['user' => $user->getId()]);

    }

}