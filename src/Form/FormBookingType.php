<?php
declare(strict_types=1);

namespace App\Form;

use App\Entity\FormBookingEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class FormBookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Arrival date field
            ->add('arrivalDate', DateType::class, [
                'label'       => 'booking.form.arrival_date',
                'required'    => true,
                'widget'      => 'single_text',
                'html5'       => true,
                'constraints' => [
                    new Assert\NotBlank(message: 'booking.validation.arrival_date_required'),
                ],
                'attr' => [
                    'class'       => 'form-control',
                    'placeholder' => 'booking.form.arrival_date_placeholder',
                ],
            ])
            // Departure date field
            ->add('departureDate', DateType::class, [
                'label'       => 'booking.form.departure_date',
                'required'    => true,
                'widget'      => 'single_text',
                'html5'       => true,
                'constraints' => [
                    new Assert\NotBlank(message: 'booking.validation.departure_date_required'),
                ],
                'attr' => [
                    'class'       => 'form-control',
                    'placeholder' => 'booking.form.departure_date_placeholder',
                ],
            ])
            // Number of persons (stored as string on entity)
            ->add('numberOfPersons', TextType::class, [
                'label'       => 'booking.form.number_of_persons',
                'required'    => true,
                'empty_data'  => '',
                'constraints' => [
                    new Assert\NotBlank(message: 'booking.validation.persons_required'),
                    new Assert\Regex(pattern: '/^(?:[1-9]|1[0-9]|20)$/', message: 'booking.validation.persons_invalid'),
                ],
                'attr' => [
                    'class'       => 'form-control',
                    'type'        => 'number',
                    'min'         => 1,
                    'max'         => 20,
                    'placeholder' => 'booking.form.number_placeholder',
                ],
            ])
            // Guest/Contact name
            ->add('contactName', TextType::class, [
                'label'       => 'booking.form.name',
                'required'    => true,
                'empty_data'  => '',
                'constraints' => [
                    new Assert\NotBlank(message: 'booking.validation.name_required'),
                    new Assert\Length(
                        min: 2,
                        max: 255,
                        minMessage: 'booking.validation.name_min',
                        maxMessage: 'booking.validation.name_max'
                    ),
                ],
                'attr' => [
                    'class'       => 'form-control',
                    'placeholder' => 'booking.form.name_placeholder',
                ],
            ])
            // Email
            ->add('contactEmail', EmailType::class, [
                'label'       => 'booking.form.email',
                'required'    => true,
                'empty_data'  => '',
                'constraints' => [
                    new Assert\NotBlank(message: 'booking.validation.email_required'),
                    new Assert\Email(message: 'booking.validation.email_invalid'),
                    new Assert\Length(max: 200),
                ],
                'attr' => [
                    'class'       => 'form-control',
                    'placeholder' => 'booking.form.email_placeholder',
                ],
            ])
            // Phone
            ->add('contactPhone', TelType::class, [
                'label'       => 'booking.form.phone',
                'required'    => true,
                'empty_data'  => '',
                'constraints' => [
                    new Assert\NotBlank(message: 'booking.validation.phone_required'),
                    new Assert\Length(
                        min: 6,
                        max: 40,
                        minMessage: 'booking.validation.phone_min',
                        maxMessage: 'booking.validation.phone_max'
                    ),
                ],
                'attr' => [
                    'class'       => 'form-control',
                    'placeholder' => 'booking.form.phone_placeholder',
                ],
            ])
            // Optional message
            ->add('notes', TextareaType::class, [
                'label'       => 'booking.form.message',
                'required'    => false,
                'empty_data'  => '',
                'constraints' => [
                    new Assert\Length(max: 2000, maxMessage: 'booking.validation.message_max'),
                ],
                'attr' => [
                    'class'       => 'form-control',
                    'rows'        => 4,
                    'placeholder' => 'booking.form.message_placeholder',
                ],
            ])
            // Data protection checkbox
            ->add('dataConsent', CheckboxType::class, [
                'label'       => 'booking.form.data_consent',
                'required'    => true,
                'constraints' => [
                    new Assert\IsTrue(message: 'booking.validation.data_consent_required'),
                ],
                'attr' => [
                    'class' => 'form-check-input',
                ],
            ])
            // Honeypot fields (not persisted, should remain empty)
            ->add('website', TextType::class, [
                'mapped'   => false,
                'required' => false,
                'attr'     => [
                    'autocomplete' => 'off',
                    'tabindex'     => '-1',
                    'style'        => 'position:absolute;left:-9999px;',
                ],
            ])
            ->add('emailrep', TextType::class, [
                'mapped'   => false,
                'required' => false,
                'attr'     => [
                    'autocomplete' => 'off',
                    'tabindex'     => '-1',
                    'style'        => 'position:absolute;left:-9999px;',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FormBookingEntity::class,
        ]);
    }
}
