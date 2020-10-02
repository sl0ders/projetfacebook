<?php

namespace App\Form;

use App\Entity\User;
use Liip\ImagineBundle\Config\Filter\Argument\Size;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Vich\UploaderBundle\Form\Type\VichImageType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                "label" => " ",
                "attr" => [
                    "placeholder" => "Votre Adresse email..."
                ]
            ])
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options' => array('label' => 'Entrez un mot de passe'),
                'second_options' => array('label' => 'Entrez a nouveau le meme mot de passe'),
            ))
            ->add('firstname', TextType::class, [
                "label" => " ",
                "attr" => [
                    "placeholder" => "Entrez votre prenom..."
                ]
            ])
            ->add('lastname', TextType::class, [
                "label" => " ",
                "attr" => [
                    "placeholder" => "Entrez votre nom de famille..."
                ]
            ])
            ->add('avatarFile', VichImageType::class, [
                "label" => "Choisissez un avatar",
                "mapped" => "userAvatar"
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

