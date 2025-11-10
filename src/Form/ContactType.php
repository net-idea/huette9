<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ihr Name']
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-Mail',
                'attr' => ['class' => 'form-control', 'placeholder' => 'ihre.email@beispiel.de']
            ])
            ->add('subject', TextType::class, [
                'label' => 'Betreff',
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'Betreff']
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Nachricht',
                'attr' => ['class' => 'form-control', 'rows' => 5, 'placeholder' => 'Ihre Nachricht an uns']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
