<?php

namespace App\Form;

use App\Entity\Viajeros;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
class ViajerosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('idIPK')
            ->add('nombre' ,TextType::class,array("attr" => array("class" => "form-control col-3")))
            //->add('ci')
            //->add('fechaSalida')
            //->add('resultado')
            ->add('correo',EmailType::class ,array("attr" => array("class" => "form-control col-3")))
           // ->add('Guardar',SubmitType::class ,array("attr" => array("class" => "btn btn-success")))
            //->add('Cancelar',ButtonType::class ,array("attr" => array("class" => "btn btn-secondary",'display'=>'none')))
            //->add('notificado')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Viajeros::class,
        ]);
    }
}
