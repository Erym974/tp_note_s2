<?php

namespace App\Form;

use App\Entity\Report;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ReportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reason', TextareaType::class, [
                'label' => 'Reason',
                'attr' => [
                    'placeholder' => 'Why do you want to report this post ?',
                    'rows' => 5,
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Your comment cannot be blank.']),
                    new Length([
                        'min' => 10,
                        'max' => 255,
                        'minMessage' => "Your message must be at least {{ limit }} characters long",
                        'maxMessage' => "Your message cannot be longer than {{ limit }} characters"
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Report::class,
        ]);
    }
}
