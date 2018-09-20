<?php

namespace AppBundle\DataFixtures;
use AppBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class UserFixtures extends Fixture
{
    /**
     * @var EncoderFactoryInterface
     */
    private $encoder;

    public function __construct(EncoderFactoryInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $password = $this->encoder->getEncoder($user)->encodePassword('123456', $user->getSalt());
        $user->setUsername('sungvadan');
        $user->setPassword($password);
        $manager->persist($user);
        $manager->flush();
    }

}