<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $subscriberauthor = new User();
        $subscriberauthor->setEmail('subscriberauthor@monsite.com');
        $subscriberauthor->setRoles(['ROLE_SUBSCRIBERAUTHOR']);
        $subscriberauthor->setPassword($this->passwordEncoder->encodePassword(
            $subscriberauthor,
            'subscriberpassword'
        ));
        $manager->persist($subscriberauthor);

        $admin = new User();
        $admin->setEmail('admin@monsite.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordEncoder->encodePassword(
            $admin,
            'adminpassword'
        ));

        $manager->persist($admin);
        $manager->flush();
    }
}
