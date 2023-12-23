<?php

namespace App\Controller\Dashboard\Crud;

use App\Entity\Report;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ReportCrudController extends AbstractCrudController
{

    public function __construct(private EntityManagerInterface $manager)
    {
        
    }
    
    public static function getEntityFqcn(): string
    {
        return Report::class;
    }

    public function configureActions(Actions $actions): Actions
    {

        $deleteAction = Action::new('delete_post', 'Delete the relevant post')
            ->linkToCrudAction('deletePost');

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $deleteAction)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::EDIT);
    }

    public function configureCrud(Crud $crud) : Crud
    {
        return Crud::new()
            ->setPageTitle('index', 'Reports')
            ->setEntityPermission('ROLE_MODERATOR')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setSearchFields(['id', 'reason', 'post.id', 'author.email', 'author.username']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('id')
            ->add('author')
            ->add('post')
            ->add('reason')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'Report ID')->setDisabled(true),
            AssociationField::new('author')->setDisabled(true),
            AssociationField::new('post', 'Post')->setDisabled(true)->setDefaultColumns('id'),
            TextEditorField::new('post.content', 'Post content')->setDisabled(true),
            TextEditorField::new('reason')->setDisabled(true),
        ];
    }

    public function deletePost(AdminContext $context)
    {
        $id     = $context->getRequest()->query->get('entityId');
        $report = $this->manager->getRepository(Report::class)->find($id);

        $post = $report->getPost();

        $this->manager->remove($report);
        $this->manager->flush();

        $this->addFlash('success', 'The report has been deleted.');
        $referer = $context->getRequest()->headers->get('referer');
        return $this->redirect($referer);
    }
}
