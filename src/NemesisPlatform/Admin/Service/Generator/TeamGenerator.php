<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 06.02.2015
 * Time: 13:16
 */

namespace NemesisPlatform\Admin\Service\Generator;

use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use NemesisPlatform\Components\Form\FormInjectorInterface;
use NemesisPlatform\Game\Entity\Participant;
use NemesisPlatform\Game\Entity\Team;
use Symfony\Component\Form\FormBuilderInterface;

class TeamGenerator implements EntityGeneratorInterface, FormInjectorInterface
{
    /** @var EntityManagerInterface */
    private $manager;

    /**
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param array $data
     *
     * @return GenerationReportInterface
     */
    public function generate($data = [])
    {
        $report  = new GenerationReport();
        $repData = [];

        $rows = $data[$this->getType().'_rows'];

        $faker = Factory::create('ru_RU');

        foreach ($rows as $key => $row) {
            if (!$this->isRowValid($row)) {
                $report->addNotification(sprintf('Invalid row %d', $key));
                continue;
            }
            /** @var \NemesisPlatform\Game\Entity\Participant $sData */
            $sData = $row;
            if ($sData->getTeams()->count() > 0) {
                $report->addNotification(sprintf('Participant %d has already has team', $key));
                continue;
            }

            $team = new Team($faker->sentence(5, true), $sData);

            $this->manager->persist($team);

            $repData[$sData->getId()]['team']    = $team;
            $repData[$sData->getId()]['name']    = $team->getName();
            $repData[$sData->getId()]['captain'] = $sData;
        }

        $this->manager->flush();

        foreach ($repData as $id => $row) {
            /** @var \NemesisPlatform\Game\Entity\Team $team */
            $team = $row['team'];

            $repData[$id]['id'] = $team->getID();
            $report->addDataRow($repData[$id]);
        }

        return $report;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'team_generator';
    }

    /**
     * @param $row array
     *
     * @return bool
     */
    protected function isRowValid(&$row)
    {
        if (!($row = $this->manager->getRepository(Participant::class)->find($row))) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Генератор команд';
    }

    public function injectForm(FormBuilderInterface $builder)
    {
        $builder
            ->add(
                $this->getType().'_rows',
                'collection',
                [
                    'allow_add'    => true,
                    'allow_delete' => true,
                    'type'         => 'integer',
                    'options'      => ['required' => true],
                ]
            );
    }
}
