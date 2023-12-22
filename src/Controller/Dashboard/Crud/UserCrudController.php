<?php

namespace App\Controller\Dashboard\Crud;

use App\Entity\User;
use DateTime;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud) : Crud
    {
        return Crud::new()
            ->setPageTitle('index', 'Users')
            ->setEntityPermission('ROLE_ADMIN')
            ->setDefaultSort(['lastConnectedAt' => 'DESC'])
            ->setSearchFields(['id', 'username', 'email']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('username')
            ->add('email')
            ->add('lastConnectedAt')
        ;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            EmailField::new('email')->setDisabled(true),
            TextField::new('username'),
            DateTimeField::new('lastConnectedAt')->setDisabled(true),
        ];
    }
}
