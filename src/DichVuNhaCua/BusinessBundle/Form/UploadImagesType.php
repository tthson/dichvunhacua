<?php
/**
 * Created by PhpStorm.
 * User: tranthaison
 * Date: 11/21/17
 * Time: 11:31 PM
 */

namespace DichVuNhaCua\BusinessBundle\Form;

use DichVuNhaCua\BusinessBundle\Entity\BusinessImages;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UploadImagesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',TextType::class, array(
                'required'   => false
            ))
            ->add('fileName',TextType::class)
            ->add('image', FileType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => "DichVuNhaCua\BusinessBundle\Entity\BusinessImages",
        ));
    }

    public function getName()
    {
        return 'business_image';
    }

}