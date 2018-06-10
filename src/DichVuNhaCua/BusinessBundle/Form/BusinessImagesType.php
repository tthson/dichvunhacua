<?php
/**
 * Created by PhpStorm.
 * User: tranthaison
 * Date: 11/21/17
 * Time: 11:31 PM
 */

namespace DichVuNhaCua\BusinessBundle\Form;

use DichVuNhaCua\BusinessBundle\Entity\Business;
use DichVuNhaCua\BusinessBundle\Entity\BusinessImages;
use DichVuNhaCua\BusinessBundle\Entity\Repository\BusinessRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class BusinessImagesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('businessId', TextType::class)
            ->add('image', VichImageType::class, array(
                'required'      => true,
                'mapped'       => 'business',
                'allow_delete'  => true,
                'by_reference'  => false
            ))
            /*->addEventListener(
                FormEvents::POST_SET_DATA,
                array($this, 'onPostSetData')
            )
            ->addEventListener(
                FormEvents::PRE_SUBMIT,
                array($this, 'onPreSubmitData')
            )*/
        ;
    }

    public function onPostSetData(FormEvent $event)
    {
        $form = $event->getForm();
        if (empty($event->getData())) {
            unset($form['businessId']);
        } else {
            $business = $event->getForm()->getParent()->getParent()->getData();
            $form['businessId'] = $business->getId();
        }
    }

    public function onPreSubmitData(FormEvent $event)
    {
        $business = $event->getForm()->getParent()->getParent()->getData();
        $form = $event->getForm();
        $form->add('businessId', TextType::class, array("data"=>$business->getId()));
        dump($form);exit;
        //$form['business'] = $business->getId();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => BusinessImages::class,
        ));
    }

    public function getName()
    {
        return 'business_images';
    }

}