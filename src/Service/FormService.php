<?php

namespace App\Service;

use App\Entity\Comment;
use App\Entity\Post;
use Symfony\Component\HttpFoundation\Request;

class FormService extends AbstractService
{


    public function handleNewPost(mixed $form, Post $post) : bool
    {

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setAuthor($this->getUser());
            $this->manager->persist($post);
            $this->manager->flush();
            $this->addFlash('success', 'Post created');
            return true;
        }
        return false;

    }

    public function handleEditPost(mixed $form, Post $post) : bool
    {

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($post);
            $this->manager->flush();
            $this->addFlash('success', 'Post edited');
            return true;
        }
        return false;

    }

    public function handleNewComment() : bool
    {

        if($this->request->isMethod('POST')) {
            $content = $this->request->request->get('content');
            $post = $this->manager->getRepository(Post::class)->find($this->request->request->get('post'));
            $reply =$this->manager->getRepository(Comment::class)->find($this->request->request->get('reply') ?? 0) ?? null;
            
            $comment = (new Comment())
                ->setAuthor($this->getUser())
                ->setPost($post)
                ->setReply($reply)
                ->setContent($content);

            $this->manager->persist($comment);
            $this->manager->flush();

            return true;
        }

        return false;

    }


}