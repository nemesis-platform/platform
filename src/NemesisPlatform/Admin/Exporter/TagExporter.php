<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 26.06.2015
 * Time: 16:47
 */

namespace NemesisPlatform\Admin\Exporter;

use Doctrine\ORM\EntityManagerInterface;
use NemesisPlatform\Components\ExportImport\Service\ExporterInterface;
use NemesisPlatform\Components\Form\FormInjectorInterface;
use NemesisPlatform\Core\Account\Entity\Tag;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class TagExporter extends AbstractCSVExporter implements ExporterInterface, FormInjectorInterface
{
    /** @var  EntityManagerInterface */
    private $manager;

    /**
     * TagExporter constructor.
     *
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param array $options
     *
     * @return Response
     */
    public function export(array $options = [])
    {
        /** @var \NemesisPlatform\Core\Account\Entity\Tag[] $tags */
        $tags = $options['tags']->toArray();

        if (count($tags) === 0) {
            throw new \InvalidArgumentException('No tags selected');
        }

        $filename = tempnam(sys_get_temp_dir(), 'exporter_');

        $zip = new \ZipArchive();
        $zip->open($filename);

        $tagsContentArray = [['tag_id', 'tag_name']];
        foreach ($tags as $tag) {
            $tagsContentArray[] = [$tag->getId(), $tag->getName()];
        }

        $tagsContent = '';
        foreach ($tagsContentArray as $row) {
            $tagsContent .= implode($this->delimiter, $this->escapeChunks($row)).PHP_EOL;
        }

        $placeholder = implode(
            ',',
            array_map(
                function (Tag $tag) {
                    return '?';
                },
                $tags
            )
        );

        $zip->addFromString('tags.csv', $tagsContent);
        $zip->addFromString(
            'user_tags.csv',
            $this->exportTags($tags, "SELECT * FROM user_tag WHERE tag_id IN ($placeholder)")
        );
        $zip->addFromString(
            'team_tags.csv',
            $this->exportTags($tags, "SELECT * FROM team_tags WHERE tag_id IN ($placeholder)")
        );

        $zip->close();

        $response = new BinaryFileResponse($filename);
        $response->setContentDisposition(
            'attachment',
            'tags_exporter.zip'
        );


        $response->deleteFileAfterSend(true);

        return $response;
    }

    /**
     * @param \NemesisPlatform\Core\Account\Entity\Tag[] $tags
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function exportTags(array $tags, $query)
    {
        $headers = ['tag_id', 'user_id'];
        sort($headers);
        $conn = $this->manager->getConnection();

        $ids = array_map(
            function (Tag $tag) {
                return $tag->getId();
            },
            $tags
        );

        $sth = $conn->prepare($query);

        $sth->execute($ids);
        $userTags = $sth->fetchAll();


        $content = implode($this->delimiter, $this->escapeChunks($headers)).PHP_EOL;
        foreach ($userTags as $tag) {
            ksort($tag);
            $content .= implode($this->delimiter, $this->escapeChunks($tag)).PHP_EOL;
        }

        return $content;
    }

    /**
     * @return string Name key for the object
     */
    public function getType()
    {
        return 'tag_exporter';
    }

    public function injectForm(FormBuilderInterface $builder)
    {
        $builder->add(
            'tags',
            'entity',
            [
                'class' => Tag::class,
                'multiple' => true,
                'required' => true,
                'expanded' => true,
            ]
        );
    }
}
