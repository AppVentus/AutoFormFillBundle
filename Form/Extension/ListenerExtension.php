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
    private $enabled;

    public function __construct($em, $filler, $request, $enabled)
    {
        $this->em = $em;
        $this->filler = $filler;
        $this->request = $request;
        $this->enabled = $enabled;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->enabled && $this->request->getMethod() !== 'POST') {
            $builder
                ->addEventSubscriber(new AutoFillSubscriber($builder, $this->em, $this->filler, $options));
        }
    }

    public function getExtendedType()
    {
        return 'form';
    }
}
