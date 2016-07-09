<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 06.02.2015
 * Time: 13:16
 */

namespace NemesisPlatform\Admin\Service\Generator;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use NemesisPlatform\Components\Form\FormInjectorInterface;
use NemesisPlatform\Core\Account\Entity\User;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class UserGenerator implements EntityGeneratorInterface, FormInjectorInterface
{
    /**
     * @var array
     */
    protected $rowKeys
        = [
            'email'          => true,
            'password'       => true,
            'lastName'       => false,
            'firstNameMale'  => false,
            'middleNameMale' => false,
        ];

    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /** @var EncoderFactoryInterface */
    private $encoder;

    /**
     * @param EntityManagerInterface  $manager
     * @param EncoderFactoryInterface $encoderFactory
     */
    public function __construct(
        EntityManagerInterface $manager,
        EncoderFactoryInterface $encoderFactory
    ) {
        $this->manager = $manager;
        $this->encoder = $encoderFactory;
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

        foreach ($data[$this->getType().'_rows'] as $key => $row) {
            if (!$this->isRowValid($row)) {
                $report->addNotification(sprintf('Invalid row %d', $key));
                continue;
            }

            $user = new User();
            $user->setEmail($row['email']);
            $user->setStatus(User::EMAIL_CONFIRMED);
            $user->setLastname($row['lastName']);
            $user->setFirstname($row['firstNameMale']);
            $user->setMiddlename($row['middleNameMale']);
            $user->setRegisterDate(new DateTime());
            $user->setBirthdate(new DateTime());
            $user->setAdminComment('Демо аккаунт');
            $user->setPassword($this->encoder->getEncoder($user)->encodePassword($row['password'], $user->getSalt()));

            $this->manager->persist($user);

            $repData[$user->getEmail()]['user']     = $user;
            $repData[$user->getEmail()]['password'] = $row['password'];
            $repData[$user->getEmail()]['email']    = $row['email'];
        }

        $this->manager->flush();

        foreach ($repData as $email => $row) {
            /** @var \NemesisPlatform\Core\Account\Entity\User $user */
            $user = $row['user'];

            $repData[$email]['id'] = $user->getID();
            $report->addDataRow($repData[$email]);
        }

        return $report;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'user_generator';
    }

    /**
     * @param $row array
     *
     * @return bool
     */
    protected function isRowValid(&$row)
    {
        $faker = Factory::create('ru_RU');

        if (!is_array($row) && $row !== '' && $row !== null) {
            $row = ['email' => $row];
        } else {
            $row = [];
        }

        if (!array_key_exists('password', $row)) {
            $row['password'] = substr(sha1(uniqid('gen_pwd_', true)), 0, 8);
        }

        if (!array_key_exists('email', $row)) {
            $row['email'] = 'demo_'.substr(sha1(uniqid('gen_email_', true)), 0, 8).'@demo_account';
        }

        foreach ($this->rowKeys as $key => $required) {
            if (!array_key_exists($key, $row)) {
                if ($required) {
                    return false;
                }

                $row[$key] = $faker->$key();
            }
        }

        return true;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Генератор учетных записей пользователей';
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
                    'type'         => 'text',
                    'options'      => ['required' => false],
                ]
            );
    }
}
