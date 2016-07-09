<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 07.05.2014
 * Time: 12:02
 */

namespace NemesisPlatform\Modules\Game\Core\Entity\ArrayDecision;

use NemesisPlatform\Modules\Game\Core\Entity\Decision;
use Ramsey\Uuid\Uuid;

final class ArrayDecisionDataRecord
{
    /** @var  int|null */
    private $id = null;
    /** @var string */
    private $key;
    /** @var mixed */
    private $value;
    /** @var Decision */
    private $decision;

    /**
     * ArrayDecisionDataRecord constructor.
     *
     * @param Decision $decision
     * @param string   $key
     * @param mixed    $value
     */
    public function __construct(Decision $decision, $key, $value)
    {
        $this->id       = Uuid::uuid4();
        $this->decision = $decision;
        $this->key      = $key;
        $this->value    = $value;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return Decision
     */
    public function getDecision()
    {
        return $this->decision;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    public function attach(Decision $decision)
    {
        $this->decision = $decision;
    }
}
