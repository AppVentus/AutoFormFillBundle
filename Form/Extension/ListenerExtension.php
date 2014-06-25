<?php
namespace AppVentus\AutoFormFillBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use AppVentus\AutoFormFillBundle\Listener\AutoFillSubscriber;

class ListenerExtension extends AbstractTypeExtension
{
    private $em;
    private $filler;
    private $request;

    public function __construct($em, $filler, $request)
    {
        $this->em = $em;
        $this->filler = $filler;
        $this->request = $request;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->request->getMethod() !== 'POST') {
            $builder
                ->addEventSubscriber(new AutoFillSubscriber($builder, $this->em, $this->filler, $options));
        }
    }

    public function getExtendedType()
    {
        return 'form';
    }
}
