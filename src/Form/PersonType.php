<?php
namespace App\Form;

use App\Entity\Person;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'first name',
                'required' => false,
            ])
            ->add('middlename', TextType::class, [
                'label' => 'middle name',
                'required' => false,
                'empty_data' => '',
            ])
            ->add('lastname', TextType::class, [
                'label' => 'last name',
                'required' => false,
            ])
            ->add('alias', TextType::class, [
                'label' => 'alias/initials',
                'required' => false,
                'empty_data' => '',
            ])
            ->add('email', EmailType::class, [
                'label' => 'email',
                'required' => false,
            ])
            ->add('phone', TextType::class, [
                'label' => 'phone number',
                'required' => false,
            ])
            ->add('address', TextType::class, [
                'label' => 'street address (1)',
                'required' => false,
            ])
            ->add('secondary_address', TextType::class, [
                'label' => 'street address (2)',
                'required' => false, 'empty_data' => '',
            ])
            ->add('city', TextType::class, [
                'label' => 'city',
                'required' => false,
            ])
            ->add('state', TextType::class, [
                'label' => 'state',
                'required' => false,
            ])
            ->add('postal_code', TextType::class, [
                'label' => 'zip',
                'required' => false,
            ])
            ->add('fee', TextType::class, [
                'label' => 'fee',
                'required' => false,
            ])
            ->add('payer', EntityType::class, [
                'class' => Person::class,
                'choice_label' => function (Person $person) {
                    return sprintf('%s %s', $person->getFirstname(), $person->getLastname());
                },
                'autocomplete' => true, // UX Autocomplete magic
                'required' => false, // Allow empty selection
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'status',
                'choices' => [
                    'patient' => 'patient',
                    'payer only' => 'payer',
                ],
                'expanded' => true, // Radio buttons instead of a dropdown
                'required' => true,
            ])
            ->add('active', ChoiceType::class, [
                'label' => 'active?',
                'choices' => [
                    'yes' => true,
                    'no' => false,
                ],
                'expanded' => true,
                'required' => true,
            ])
            ->add('notes', TextareaType::class, ['empty_data' => '', 'required' => false])
            ->add('save', SubmitType::class, [
                'label' => 'Save',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Person::class,
        ]);
    }
}
