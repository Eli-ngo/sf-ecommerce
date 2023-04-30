<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Filter\UserLastSignup;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\{Action, Actions, Crud, KeyValueStore, Filters};
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\{IdField, EmailField, TextField, ArrayField, DateTimeField};
use Symfony\Component\Form\Extension\Core\Type\{PasswordType, RepeatedType};
use Symfony\Component\Form\{FormBuilderInterface, FormEvent, FormEvents};
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCrudController extends AbstractCrudController
{
    public function __construct(
        public UserPasswordHasherInterface $userPasswordHasher
    ) {}

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    // public function configureFilters(Filters $filters): Filters
    // {
    //     return $filters 
    //         -> add('createdAt', ChoiceFilter::class, [
    //         'label' => 'Inscrit aujourd\'hui',
    //         'field_options' => [
    //             'choices' => [
    //                 'Oui' => true,
    //                 'Non' => false,
    //             ],
    //         ],
    //         'apply_filter' => function (QueryBuilder $queryBuilder, $filterData, $filterForm) {
    //             if ($filterData['value'] === true) {
    //                 $queryBuilder
    //                     ->andWhere('entity.createdAt >= :today')
    //                     ->setParameter('today', new \DateTime('today'))
    //                 ;
    //             } elseif ($filterData['value'] === false) {
    //                 $queryBuilder
    //                     ->andWhere('entity.createdAt < :today')
    //                     ->setParameter('today', new \DateTime('today'))
    //                 ;
    //             }
    //         },
    //     ]);
    // }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setFormOptions(
            ['validation_groups' => ['Default', 'creation']], ['validation_groups' => ['Default', 'creation']]
        );
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(UserLastSignup::new('createdAt'))
            ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::DETAIL)
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        $fields = [
            IdField::new('id')->hideOnForm(),
            EmailField::new('email'),
            TextField::new('firstname'),
            TextField::new('lastname'),
            ArrayField::new('roles'),
            DateTimeField::new('createdAt')->hideOnForm(),
        ];

        $password = TextField::new('password')
            ->setFormType(RepeatedType::class)
            ->setFormTypeOptions([
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => '(Repeat)'],
                'mapped' => false,
            ])
            ->setRequired($pageName === Crud::PAGE_NEW)
            ->onlyOnForms()
            ;
        $fields[] = $password;

        return $fields;
    }

    public function createNewFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $formBuilder = parent::createNewFormBuilder($entityDto, $formOptions, $context);
        return $this->addPasswordEventListener($formBuilder);
    }

    public function createEditFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $formBuilder = parent::createEditFormBuilder($entityDto, $formOptions, $context);
        return $this->addPasswordEventListener($formBuilder);
    }

    private function addPasswordEventListener(FormBuilderInterface $formBuilder): FormBuilderInterface
    {
        return $formBuilder->addEventListener(FormEvents::POST_SUBMIT, $this->hashPassword());
    }

    private function hashPassword() {
        return function($event) {
            $form = $event->getForm();
            if (!$form->isValid()) {
                return;
            }
            $password = $form->get('password')->getData();
            if ($password === null) {
                return;
            }

            $hash = $this->userPasswordHasher->hashPassword($this->getUser(), $password);
            $form->getData()->setPassword($hash);
        };
    }
}
