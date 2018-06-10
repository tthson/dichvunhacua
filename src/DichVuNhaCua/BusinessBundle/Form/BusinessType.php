<?php

namespace DichVuNhaCua\BusinessBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class BusinessType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('logo', HiddenType::class, array(
                "required" => false
            ))
            ->add('address')
            ->add('landmark')
            ->add('zip')
            ->add('phone')
            ->add('email')
            ->add('website')
            ->add('fax', TextType::class,array(
                'required'   => false,
                'empty_data' => 'Please enter fax.',
            ))
            ->add('about', TextareaType::class, array(
                'attr' => array('class' => 'tinymce')))
            ->add('workingHours', TextareaType::class, array(
                'attr' => array('class' => 'tinymce')))
            ->add('employees')
            //->add('entity')
            //->add('size')
            ->add('certification')
            //->add('contactPerson')
            //->add('ratting')
            //->add('agree')
            //->add('long')
            //->add('lat')
            //->add('featured')
            //->add('verified')
            //->add('status')
            //->add('viewed')
            ->add('categories', EntityType::class, array(
                'class' => 'DichVuNhaCua\BusinessBundle\Entity\Categories',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },
                'choice_label' => 'name',
                'expanded' => false,
                'multiple' => true,
                'by_reference' => false,
            ))
            ->add('images', CollectionType::class, array(
                'entry_type' => UploadImagesType::class,
                'entry_options' => array('label' => false),
                'allow_add' => true,
                'by_reference' => false,
            ))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DichVuNhaCua\BusinessBundle\Entity\Business'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'dichvunhacua_businessbundle_business';
    }


}
