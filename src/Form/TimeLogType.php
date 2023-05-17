<?php

namespace App\Form;

use App\Entity\TimeLog;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotNull;

class TimeLogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startTime', DateTimeType::class, [
                'label' => 'Start Time',
                'widget' => 'single_text',
                'attr' => ['class' => 'datetimepicker'],
                'constraints' => [
                    new NotNull(),
                    new GreaterThan('now'),
                ],
            ])
            ->add('endTime', DateTimeType::class, [
                'label' => 'End Time',
                'widget' => 'single_text',
                'attr' => ['class' => 'datetimepicker'],
                'constraints' => [
                    new NotNull(),
                    new GreaterThan('now'),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TimeLog::class,
        ]);
    }
}