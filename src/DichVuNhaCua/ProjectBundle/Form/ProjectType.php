<?php

namespace DichVuNhaCua\ProjectBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ProjectType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('userId')
            ->add('name')
            ->add('categoryId')
            ->add('address')
            ->add('city')
            ->add('zip')
            ->add('phone')
            ->add('email')
            ->add('firstName')
            ->add('lastName')
            ->add('isFreeProjectCost')
            ->add('projectStatus')
            ->add('projectPeriod')
            ->add('projectLocationType')
            //->add('state')
            ->add('detail', TextareaType::class, array(
                'attr' => array('class' => 'tinymce')))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DichVuNhaCua\ProjectBundle\Entity\Project'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'dichvunhacua_projectbundle_project';
    }


}
