<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 19.05.2015
 * Time: 13:58
 */

namespace NemesisPlatform\Admin\Importer;

use Doctrine\ORM\EntityManagerInterface;
use NemesisPlatform\Components\ExportImport\PostProcessor\DoctrinePersistPostprocessor;
use NemesisPlatform\Components\ExportImport\PostProcessor\ImportPostProcessorInterface;
use NemesisPlatform\Components\ExportImport\Service\ImporterInterface;
use NemesisPlatform\Components\ExportImport\Service\ImportResultInterface;
use NemesisPlatform\Components\ExportImport\Service\SimpleResult;
use NemesisPlatform\Components\Form\FormInjectorInterface;
use NemesisPlatform\Components\MultiSite\Service\SiteProviderInterface;
use NemesisPlatform\Core\Account\Entity\User;
use NemesisPlatform\Game\Entity\Certificate\CertificateType;
use NemesisPlatform\Game\Entity\Certificate\ParticipantCertificate;
use NemesisPlatform\Game\Entity\Season;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;

class ParticipantCertificateImporter implements ImporterInterface, FormInjectorInterface
{
    /** @var  ParticipantCertificate[] */
    protected $data;

    /** @var  FormFactoryInterface */
    protected $factory;

    /** @var ImportPostProcessorInterface[] */
    protected $processors = [];

    /** @var array Options */
    protected $options = [];

    /** @var EntityManagerInterface */
    protected $manager;
    /** @var  SiteProviderInterface */
    protected $siteManager;

    /**
     * SharesImporter constructor.
     *
     * @param SiteProviderInterface  $siteManager
     * @param FormFactoryInterface   $factory
     * @param EntityManagerInterface $manager
     */
    public function __construct(
        SiteProviderInterface $siteManager,
        FormFactoryInterface $factory,
        EntityManagerInterface $manager
    ) {
        $this->siteManager = $siteManager;
        $this->factory     = $factory;
        $this->manager     = $manager;
        $this->processors  = [new DoctrinePersistPostprocessor($manager)];
    }

    /** @return bool */
    public function isValid()
    {
        if (!array_key_exists('season', $this->options) && $this->options['season'] instanceof Season) {
            return false;
        }

        if (!array_key_exists('type', $this->options) && $this->options['type'] instanceof CertificateType) {
            return false;
        }

        return $this->data !== null;
    }

    /** @return ImportResultInterface */
    public function getResult()
    {
        return new SimpleResult($this->data);
    }

    /** @param ImportPostProcessorInterface $processor */
    public function registerPostProcessor(ImportPostProcessorInterface $processor)
    {
        $this->processors[] = $processor;
    }

    /** @return ImportPostProcessorInterface[] */
    public function getPostProcessors()
    {
        return $this->processors;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Импорт сертификаторв';
    }

    /** @param mixed $data */
    public function setup($data)
    {
        $text = $data['certs'];
        /** @var CertificateType $type */
        $type = $data['type'];
        /** @var \NemesisPlatform\Game\Entity\Season $season */
        $season = $data['season'];

        $strings = explode("\n", $text);

        $this->data = [];

        foreach ($strings as $string) {
            list($userId, $link) = sscanf($string, '%d %s');

            $user = $this->manager->getRepository(User::class)->find($userId);
            if (!$user) {
                continue;
            }

            $cert         = new ParticipantCertificate($link, $type, $user, $season);
            $this->data[] = $cert;
        }
    }

    public function injectForm(FormBuilderInterface $builder)
    {
        $builder
            ->add(
                'season',
                'site_seasons',
                [
                    'attr' => [
                        'help_text' => 'Выберите сезон',
                    ],
                ]
            )
            ->add(
                'type',
                'entity',
                [
                    'label'       => 'Тип сертификата',
                    'class'       => CertificateType::class,
                    'multiple'    => false,
                    'required'    => true,
                    'property'    => 'name',
                    'placeholder' => 'Выберите тип',
                    'attr'        => [
                        'help_text' => 'Выберите тип сертификата',
                    ],
                ]
            )
            ->add('certs', 'textarea', ['label' => 'Таблица']);
    }
}
