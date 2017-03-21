<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class AdjectiveType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', TextType::class)
        	->add('weight', ChoiceType::class, array(
        	  	  	'choices' => array("-1" => -1, "-0.5" => -0.5, "0.5" => 0.5, "1" => 1)));
    }
}