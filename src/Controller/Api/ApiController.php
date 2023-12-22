<?php

namespace App\Controller\Api;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class ApiController extends AbstractController
{

    #[Route(path: '/api/like/{id}', name: 'api.like')]
    public function like(Post $post, EntityManagerInterface $manager): JsonResponse
    {

        /** @var User */
        $user = $this->getUser();

        $liked = false;

        if ($post->getLikes()->contains($user)) {
            $post->removeLike($user);
        } else {
            $post->addLike($user);
            $liked = true;
        }

        $manager->flush();

        return $this->json([
            'success' => true,
            'liked' => $liked,
            'likes' => $post->getLikes()->count()
        ]);

    }

}