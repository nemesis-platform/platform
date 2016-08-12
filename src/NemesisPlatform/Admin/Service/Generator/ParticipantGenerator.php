<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 06.02.2015
 * Time: 13:16
 */

namespace NemesisPlatform\Admin\Service\Generator;

use Doctrine\ORM\EntityManagerInterface;
use NemesisPlatform\Components\Form\FormInjectorInterface;
use NemesisPlatform\Core\Account\Entity\User;
use NemesisPlatform\Game\Entity\Participant;
use Symfony\Component\Form\FormBuilderInterface;

class ParticipantGenerator implements EntityGeneratorInterface, FormInjectorInterface
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

        $season = $data['season'];
        $rows   = $data[$this->getType().'_rows'];

        foreach ($rows as $key => $row) {
            if (!$this->isRowValid($row)) {
                $report->addNotification(sprintf('Invalid row %d', $key));
                continue;
            }
            /** @var User $user */
            $user = $row;
            if ($user->getParticipation($season)) {
                $report->addNotification(sprintf('User %d already registered for season ', $user->getID()));
                continue;
            }

            $sData = new Participant();
            $sData->setSeason($season);
            $sData->setUser($user);

            $this->manager->persist($sData);

            $repData[$user->getID()]['user']  = $user;
            $repData[$user->getID()]['sData'] = $sData;
        }

        $this->manager->flush();

        foreach ($repData as $id => $row) {
            /** @var \NemesisPlatform\Game\Entity\Participant $sData */
            $sData = $row['sData'];

            $repData[$id]['id'] = $sData->getId();
            $report->addDataRow($repData[$id]);
        }

        return $report;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'participant_generator';
    }

    /**
     * @param $row array
     *
     * @return bool
     */
    protected function isRowValid(&$row)
    {
        if (!($row = $this->manager->getRepository(User::class)->find($row))) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Генератор анкет сезонов пользователей';
    }

    public function injectForm(FormBuilderInterface $builder, array $options = [])
    {
        $builder
            ->add('season', 'site_seasons', ['label' => 'Сезон'])
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
