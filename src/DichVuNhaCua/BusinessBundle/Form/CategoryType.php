<?php

namespace DichVuNhaCua\BusinessBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CategoryType
 *
 * @package DichVuNhaCua\BusinessBundle\Form
 */
class CategoryType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id')
            ->add('languageId')
            ->add('parentId')
            ->add('lineage')
            ->add('deep')
            ->add('name')
            ->add('description')
            ->add('metaTitle')
            ->add('metaKeywords')
            ->add('metaDescription')
            ->add('slug')
            ->add('status')
            ->add('icon')
            ->add('popular');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'DichVuNhaCua\BusinessBundle\Entity\Categories',
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'dichvunhacua_businessbundle_category';
    }
}
