<?php
namespace NemesisPlatform\Components\ExportImport;

/**
 * Interface ExportableDecision
 *
 * @package NemesisPlatform\Modules\Game\Core\Entity
 */
interface Exportable
{
    /**
     * @return string content to put to filename
     */
    public function getContent();

    /**
     * @return string preferred file name
     */
    public function generateFilename();

}
