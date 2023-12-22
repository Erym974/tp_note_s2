<?php

namespace App\Controller;

use App\Entity\Invitation;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{

    #[Route(path: '/profile/{user}', name: 'profile.index')]
    public function index(User $user, EntityManagerInterface $manager)
    {
        /** @var User */
        $me = $this->getUser();
        $posts = $manager->getRepository(Post::class)->getUserPost($user, $me);
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

        return $this->render('profile/index.html.twig', [
            'posts' => $posts,
            'profiled' => $user,
            'isFriend' => $isFriend,
            'invitation' => $invitation ?? null
        ]);
    }

}