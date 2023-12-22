<?php

namespace App\EntityListener;

use App\Entity\Post;
use Cocur\Slugify\Slugify;

class PostListener
{

    public function prePersist(Post $post)
    {
        $slugify = new Slugify();
        $post->setSlug($slugify->slugify($post->getTitle()));
    }

    public function preUpdate(Post $post) {
        $slugify = new Slugify();
        $post->setSlug($slugify->slugify($post->getTitle()));
        $post->setUpdatedAt(new \DateTimeImmutable());
    }

}