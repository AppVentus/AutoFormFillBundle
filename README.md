AutoFormFillBundle
=================

When you are developing, form manual testing is actualy very boring and time consuming.
This bundle fill automaticly creation forms in your application.

## Installation

Add this bundle to your composer.json file:

    {
        "require-dev": {
            "appventus/auto-form-fill-bundle": "dev-master"
        }
    }
Register the bundle in app/AppKernel.php:

    // app/AppKernel.php
    public function registerBundles()
    {

        if (in_array($this->getEnvironment(), array('dev'))) {
            $bundles[] = new AppVentus\AutoFormFillBundle\AvAutoFormFillBundle();
        }
    }
