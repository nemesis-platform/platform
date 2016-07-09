<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 16.03.2015
 * Time: 15:40
 */

namespace NemesisPlatform\Components\ExportImport\PostProcessor;

use Doctrine\ORM\EntityManagerInterface;
use NemesisPlatform\Components\ExportImport\Service\ImportResultInterface;

/**
 * Class DoctrinePersistPostprocessor
 *
 * Persist import data to entity manager
 *
 * @package NemesisPlatform\Components\ExportImport\PostProcessor
 */
class DoctrinePersistPostprocessor implements ImportPostProcessorInterface
{
    /** @var EntityManagerInterface */
    private $manager;

    /**
     * DoctrinePersistPostprocessor constructor.
     *
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param ImportResultInterface $result
     *
     * @return bool
     */
    public function isApplicable(ImportResultInterface $result)
    {
        foreach ($result->getData() as $record) {
            if (!$this->manager->getClassMetadata(get_class($record))) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param mixed $data Input data
     *
     * @return mixed Processed data
     */
    public function process($data)
    {
        foreach ($data as $record) {
            $this->manager->persist($record);
        }

        $this->manager->flush();
    }
}
