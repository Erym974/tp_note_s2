<?php

namespace App\EntityListener;

use App\Entity\Category;
use Cocur\Slugify\Slugify;

class CategoryListener
{

    public function prePersist(Category $category)
    {
        $slugify = new Slugify();
        $category->setSlug($slugify->slugify($category->getName()));
    }

    public function preUpdate(Category $category) {
        $slugify = new Slugify();
        $category->setSlug($slugify->slugify($category->getName()));
    }

}