<?php

namespace App\Controller\Admin;

use App\Entity\Annonces;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class AnnoncesCrudController extends AbstractCrudController
{
    public const ACTION_DUPLICATE = 'duplicate';
    public const PRODUCTS_BASE_PATH = 'upload\images\annonces';
    public const PRODUCTS_UPLOAD_DIR = 'public\assets\upload\images\annonces';

    public static function getEntityFqcn(): string
    {
        return Annonces::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $duplicate = Action::new(self::ACTION_DUPLICATE)
            ->linkToCrudAction('duplicateAnnonces');

        return $actions
            ->add(Crud::PAGE_EDIT, $duplicate);
    }



    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name', 'Label'),

            ImageField::new('image')
                ->setBasePath(self::PRODUCTS_BASE_PATH)
                ->setUploadDir(self::PRODUCTS_UPLOAD_DIR)
                ->setSortable(false),

            BooleanField::new('active'),
            TextEditorField::new('description'),
            AssociationField::new('category')->setQueryBuilder(function (QueryBuilder $queryBuilder) {
                $queryBuilder->where('entity.active = true');
            }),

            DateTimeField::new('updatedAt')->hideOnForm(),
            DateTimeField::new('createdAt')->hideOnForm(),
        ];
    }
    
    public function persistEntity(EntityManagerInterface $em, $entityInstance): void
    {
        if (!$entityInstance instanceof Annonces) return;
        $entityInstance->setCreatedAt(new \DateTimeImmutable);
        parent::persistEntity($em, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $em, $entityInstance): void
    {
        if (!$entityInstance instanceof Annonces) return;
        $entityInstance->setCreatedAt(new \DateTimeImmutable);
        parent::persistEntity($em, $entityInstance);
    }
    

    public function duplicateAnnonces(AdminContext $context, AdminUrlGenerator $adminUrlGenerator, EntityManagerInterface $em)
    {
        /** @var Annonces $annonces */
        $annonces = $context->getEntity()->getInstance();

        $duplicateAnnonces = clone $annonces;

        parent::persistEntity($em, $duplicateAnnonces);

        $url = $adminUrlGenerator->setController(self::class)
            ->setAction(Action::DETAIL)
            ->setEntityId($duplicateAnnonces->getId())
            ->generateUrl();

        return $this->redirect($url);
    }
}
