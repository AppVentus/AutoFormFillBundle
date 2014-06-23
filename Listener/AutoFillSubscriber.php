<?php
namespace AppVentus\AutoFormFillBundle\Listener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class AutoFillSubscriber implements EventSubscriberInterface
{


    private $builder;
    private $em;
    private $faker;

    public function __construct(FormBuilderInterface $builder, $em, $options)
    {
        $this->builder = $builder;
        $this->em = $em;
        $this->options = $options;
    }

    public static function getSubscribedEvents()
    {
        return array(FormEvents::PRE_SET_DATA => 'preSetData');
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (!empty($this->options['data_class'])
                &&
                ( !is_object($data)
                   || (is_object($data) && !$data->getId())
                )) {
            $dataClass = $this->options['data_class'];
            $newData = @$this->filler->populateData($dataClass);

            $event->setData($newData);
        }
    }

}
