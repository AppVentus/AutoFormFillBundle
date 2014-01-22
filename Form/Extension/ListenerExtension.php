<?php
namespace AppVentus\AutoFormFillBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use AppVentus\AutoFormFillBundle\Listener\AutoFillSubscriber;

class ListenerExtension extends AbstractTypeExtension
{
    private $em;
    private $request;

    public function __construct($em, $request)
    {
        $this->em = $em;
        $this->request = $request;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->request->getMethod() !== 'POST') {
            $builder
                ->addEventSubscriber(new AutoFillSubscriber($builder, $this->em, $options));
        }
    }

    public function getExtendedType()
    {
        return 'form';
    }
}
