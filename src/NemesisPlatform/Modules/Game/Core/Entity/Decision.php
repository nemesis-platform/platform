<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 07.05.2014
 * Time: 12:02
 */

namespace NemesisPlatform\Modules\Game\Core\Entity;

use DateTime;
use NemesisPlatform\Components\Form\FormTypedInterface;
use NemesisPlatform\Game\Entity\Team;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class Decision implements FormTypedInterface, \ArrayAccess
{
    /** @var string */
    private $id;
    /** @var Team */
    private $team;
    /** @var UserInterface */
    private $author;
    /** @var  DateTime */
    private $submissionTime;

    /**
     * Decision constructor.
     *
     * @param Team          $team
     * @param UserInterface $author
     */
    public function __construct(Team $team, UserInterface $author)
    {
        $this->submissionTime = new DateTime();
        $this->id             = Uuid::uuid4();
    }

    /**
     * @return DateTime
     */
    public function getSubmissionTime()
    {
        return $this->submissionTime;
    }

    /**
     * @return Team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @return UserInterface
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
}
