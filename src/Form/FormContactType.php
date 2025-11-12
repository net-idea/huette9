<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\FormContactEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class FormContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(message: 'Please enter your name.'),
                    new Assert\Length(
                        min: 2,
                        max: 160,
                        minMessage: 'Name must be at least {{ limit }} characters long.',
                        maxMessage: 'Name cannot be longer than {{ limit }} characters.'
                    ),
                ],
            ])
            ->add('emailAddress', EmailType::class, [
                'label' => 'Email',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(message: 'Please enter your email address.'),
                    new Assert\Email(message: 'Please enter a valid email address.'),
                    new Assert\Length(max: 200),
                ],
            ])
            ->add('phone', TelType::class, [
                'label' => 'Phone',
                'required' => false,
                'constraints' => [
                    new Assert\Length(max: 40),
                ],
            ])
            ->add('subject', TextType::class, [
                'label' => 'Subject',
                'required' => false,
                'constraints' => [
                    new Assert\Length(max: 255),
                ],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(message: 'Please enter a message.'),
                    new Assert\Length(
                        min: 10,
                        minMessage: 'Message must be at least {{ limit }} characters long.'
                    ),
                ],
            ])
            ->add('consent', CheckboxType::class, [
                'label' => 'I agree to the processing of my data',
                'required' => true,
                'constraints' => [
                    new Assert\IsTrue(message: 'You must agree to continue.'),
                ],
            ])
            ->add('copy', CheckboxType::class, [
                'label' => 'Send me a copy of this message',
                'required' => false,
            ])
            // Honeypot field (not persisted, should remain empty)
            ->add('website', TextType::class, [
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'autocomplete' => 'off',
                    'tabindex' => '-1',
                    'style' => 'position:absolute;left:-9999px;',
                ],
            ])
            ->add('emailrep', TextType::class, [
                'mapped' => true,
                'required' => false,
                'attr' => [
                    'autocomplete' => 'off',
                    'tabindex' => '-1',
                    'style' => 'position:absolute;left:-9999px;',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FormContactEntity::class,
        ]);
    }
}
