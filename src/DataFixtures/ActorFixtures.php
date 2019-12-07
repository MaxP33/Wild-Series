<?php


namespace App\DataFixtures;

use App\Service\Slugify;
use Faker;
use App\Entity\Actor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ActorFixtures extends Fixture implements DependentFixtureInterface
{
    const ACTORS = [
        'Andrew Lincoln' => [
            'program'    => 'program_0',
        ],
        'Norman Reedus' => [
            'program'    => 'program_0',
        ],
        'Lauren Cohan' => [
            'program'    => 'program_0',
        ],
        'Danai Gurira' => [
            'program'    => 'program_0',
        ],
    ];
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        $slugify = new Slugify();
        for ($i = 0; $i < 50; $i++) {
            $actor = new Actor();
            $actor->setName($faker->name);
            $slug = $slugify->generate($actor->getName());
            $actor->setSlug($slug);
            $manager->persist($actor);
            $actor->addProgram($this->getReference('program_' . rand(0,5)));
        }
        foreach (self::ACTORS as $name => $data) {
        $actor = new Actor();
        $actor->setName($name);

        $slug = $slugify->generate($actor->getName());
        $actor->setSlug($slug);
        $manager->persist($actor);
        $actor->addProgram($this->getReference($data['program']));
    }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [ProgramFixtures::class];
    }
}