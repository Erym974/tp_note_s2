<?php

namespace App\Service;

use App\Entity\Post;

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


}