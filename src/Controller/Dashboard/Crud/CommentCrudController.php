<?php

namespace App\Controller\Dashboard\Crud;

use App\Entity\Comment;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CommentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Comment::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::NEW);
    }

    public function configureCrud(Crud $crud) : Crud
    {
        return Crud::new()
            ->setPageTitle('index', 'Comments')
            ->setEntityPermission('ROLE_MODERATOR')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setSearchFields(['id', 'content', 'author.email', 'author.username']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('post')
            ->add('createdAt')
            ->add('author')
        ;
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            AssociationField::new('author')->setDisabled(true),
            AssociationField::new('post')->setDisabled(true),
            TextEditorField::new('content')->onlyOnIndex(),
            TextareaField::new('content')->hideOnIndex(),
            AssociationField::new('reply')->setDisabled(true),
            DateTimeField::new('createdAt')->onlyOnIndex(),
        ];
    }

}
