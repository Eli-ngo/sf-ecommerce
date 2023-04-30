<?php

namespace App\Controller\Admin;

use App\Entity\Basket;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

/**
 * @Security("is_granted('ROLE_SUPER_ADMIN')")
 */
class BasketCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Basket::class;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $queryBuilder->andWhere('entity.state = 0');
        return $queryBuilder;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setFormOptions(
            ['validation_groups' => ['Default', 'creation']], ['validation_groups' => ['Default', 'creation']]
        );
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            idField::new('id')->hideOnForm(),
            TextField::new('user'),
            BooleanField::new('state'),
            CollectionField::new('contentBaskets')
                ->setFormTypeOption('by_reference', false),
            AssociationField::new('contentBaskets')
                ->formatValue(function ($value, $entity) {
                    $products = [];
                    foreach ($entity->getContentBaskets() as $contentBasket) {
                        $products[] = $contentBasket->getProducts()->getName() . ' x ' . $contentBasket->getQuantity();
                    }
                    return implode(', ', $products);
                })
            
        ];
    }
}
