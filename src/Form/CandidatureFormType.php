<?php


namespace App\Form;


use App\Controller\SectionsController;
use App\Entity\Candidature;
use App\Entity\Sections;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CandidatureFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $rd = new Sections();
        $rd->setNom('R&D');
        $industry = new Sections();
        $industry->setNom('Industry');
        $military = new Sections();
        $military->setNom('Military');

        $builder
            ->add('login', TextType::class)
            ->add('motDePasse', PasswordType::class)
            ->add('prenom', TextType::class, [
                'required' => false,
            ])
            ->add('nom', TextType::class, [
                'required' => false,
            ])
            ->add('section', ChoiceType::class, [
               'choices' => [
                   'R&D' => $rd,
                   'Industry' => $industry,
                   'Military' => $military,
                   'None' => null,
               ]
            ])
            ->add('motivation', TextareaType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Candidature::class,
        ]);
    }

}