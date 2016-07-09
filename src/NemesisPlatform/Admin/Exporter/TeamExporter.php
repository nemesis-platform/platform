<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 22.10.2014
 * Time: 13:31
 */

namespace NemesisPlatform\Admin\Exporter;

use NemesisPlatform\Game\Entity\Rule\RuleInterface;
use NemesisPlatform\Game\Entity\Team;

class TeamExporter extends AbstractTeamExporter
{
    /**
     * @return string
     */
    public function getType()
    {
        return 'teams_simple_exporter';
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Экспорт команд';
    }

    protected function getHeaders()
    {
        $headers = ['id', 'name', 'jid', 'captain_id', 'league', 'date', 'form_date'];

        /** @var RuleInterface[] $rules */
        $rules = [];

        foreach ($this->seasons as $season) {
            $rules = array_merge($rules, $season->getRules()->toArray());
        }

        array_unique($rules);

        foreach ($rules as $validator) {
            if (!$validator->isStrict()) {
                continue;
            }

            $headers[] = $validator->getType();
        }

        return $headers;
    }

    /**
     * @param \NemesisPlatform\Game\Entity\Team $team
     *
     * @return string[]
     */
    protected function teamToArray(Team $team)
    {
        $result = [];

        $result['id']         = $team->getID();
        $result['name']       = $team->getName();
        $result['jid']        = $team->getPersistentTag();
        $result['captain_id'] = $team->getCaptain() ? $team->getCaptain()->getId() : '';
        $result['league']     = $team->getLeague() ? $team->getLeague()->getName() : '';
        $result['date']       = $team->getDate() ? $team->getDate()->format('Y.m.d H:i:s') : '';
        $result['form_date']  = $team->getFormDate() ? $team->getFormDate()->format('Y.m.d H:i:s') : '';

        /** @var RuleInterface[] $rules */
        $rules = [];

        foreach ($this->seasons as $season) {
            $rules = array_merge($rules, $season->getRules()->toArray());
        }

        array_unique($rules);


        foreach ($rules as $validator) {
            if (!$validator->isStrict() || !$validator->isApplicable($team)) {
                continue;
            }

            if (!$team->getSeason()->getRules()->contains($validator)) {
                $result[$validator->getType()] = 0;
                continue;
            }

            if (!$validator->isEnabled()) {
                $result[$validator->getType()] = 0;
                continue;
            }

            $result[$validator->getType()] = $validator->isValid($team, $team->getSeason()) ? '1' : '-1';
        }

        return $result;
    }
}
