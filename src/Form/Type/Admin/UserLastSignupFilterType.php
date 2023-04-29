<?php
// src/Form/Type/Admin/UserLastSignupFilterType.php
namespace App\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;

class UserLastSignupFilterType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'checked' => true,
            'choices' => [
                'Aujourd\'hui' => 'today',
                'Tous' => 'all',
            ],
        ]);
        
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}