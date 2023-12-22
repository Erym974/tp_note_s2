<?php

namespace App\Controller\Redirection;

use App\Entity\Invitation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FriendsController extends AbstractController 
{
    
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