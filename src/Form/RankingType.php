<?php

namespace App\Form;

use App\Entity\Carta;
use App\Entity\Ranking;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

// SECCIÓN: Formulario de Ranking
class RankingType extends AbstractType
{
    // SECCIÓN: Construcción del Formulario
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre')
            ->add('cartasDisponibles', EntityType::class, [
                'class' => Carta::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    // SECCIÓN: Configuración de Opciones
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ranking::class,
        ]);
    }
}
