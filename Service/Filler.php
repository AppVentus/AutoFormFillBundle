<?php
namespace AppVentus\AutoFormFillBundle\Service;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Symfony\Component\Form\FormBuilderInterface;

class Filler
{

    private $em;
    private $faker;

    public function __construct($em)
    {
        $this->em = $em;
        $this->faker = \Faker\Factory::create('fr_FR');
    }

    public function completeObject(&$object) {
        $this->fillObject($object, false);
    }
    public function populateData($dataClass)
    {
        $newData = new $dataClass();
        $this->fillObject($newData, true);

    }

    private function fillObject(&$newData, $force = false)
    {
        $dataClass = get_class($newData);
        try {
            $metadata = $this->em->getClassMetadata($dataClass);
            foreach ($metadata->fieldMappings as $field => $params) {
                if (!array_key_exists('id', $params)
                    && method_exists($newData, 'set' . ucfirst($field))
                    && method_exists($newData, 'get' . ucfirst($field))
                    && ($newData->{'get' . ucfirst($field)}() == null
                        || $force == true)
                ) {
                    $newData->{'set' . ucfirst($field)}($this->resolveData($field, $params['type']));
                }
            }
            foreach ($metadata->associationMappings as $field => $params) {
                //@todo: append "s" could not work sometimes
                if (method_exists($newData, 'get' . ucfirst($field) . "s")
                    && ($newData->{'get' . ucfirst($field) . "s"}() == null
                    || $force == true)) {

                    $entities = $this->em->getRepository($params['targetEntity'])->findAll();

                    if (!count($entities)) {
                        break;
                    } else {
                        $entity = $entities[array_rand($entities)];
                    }
                    if (in_array(
                            $params['type'],
                            array(ClassMetadataInfo::ONE_TO_MANY, ClassMetadataInfo::MANY_TO_MANY)
                        )
                        && method_exists($newData, 'add' . substr_replace(ucfirst($field), "", -1))
                    ) {

                        // @todo: better way to transform "addFoos()" into "addFoo()"
                        $newData->{'add' . substr_replace(ucfirst($field), "", -1)}($entity);
                    } elseif (in_array(
                            $params['type'],
                            array(ClassMetadataInfo::ONE_TO_ONE, ClassMetadataInfo::MANY_TO_ONE)
                        ) && 'set' . ucfirst($field)
                    ) {
                        $newData->{'set' . ucfirst($field)}($entity);
                    }
                }
            }

            return $newData;

        } catch (\Exception $e) {
            return null;
        }
    }

    private function resolveData($field, $type)
    {
        $data = '';
        try {
            $data = $this->faker->$field;
        } catch (\Exception $e) {
            switch ($type) {
                case 'string':
                    $data = $this->faker->text(5);
                    break;
                case 'text':
                    $data = $this->faker->text;
                    break;
                case 'array':
                    $data = array();
                    break;
                case 'boolean':
                    $data = (bool) array_rand(array(true, false));
                    break;
                case 'integer':
                    $data = $this->faker->randomDigit(1, 100);
                    break;
                case 'float':
                    $data = $this->faker->randomFloat(2, 1, 100);
                    break;
                case 'datetime':
                    $data = new \DateTime($this->faker->date);
                    break;
            }
        }

        return $data;
    }
}