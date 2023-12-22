<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => "Title"
                ]
            ])
            ->add('content', TextareaType::class, [
                'required' => true,
                'label' => null,
                'attr' => [
                    'placeholder' => "Write your message"
                ],
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\Length([
                        'min' => 10,
                        'max' => 255,
                        'minMessage' => "Your message must be at least {{ limit }} characters long",
                        'maxMessage' => "Your message cannot be longer than {{ limit }} characters"
                    ])
                ]
            ])
            ->add('private', CheckboxType::class, [
                'required' => false,
                'label' => "Only for friends"
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'required' => false,
                'multiple' => true,
                'autocomplete' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
