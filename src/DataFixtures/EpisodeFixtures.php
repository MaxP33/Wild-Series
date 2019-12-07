<?php


namespace App\DataFixtures;
use App\Service\Slugify;
use Faker;
use App\Entity\Episode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
            $faker = Faker\Factory::create('fr_FR');
            $slugify = new Slugify();
            for ($i = 0; $i < 20; $i++) {
                $episode = new Episode();
                $episode->setTitle($faker->title);
                $episode->setNumber($faker->numberBetween(0,20));
                $episode->setSynopsis($faker->text);
                $slug = $slugify->generate($episode->getTitle());
                $episode->setSlug($slug);
                $manager->persist($episode);
                $episode->setSeason($this->getReference('season_' . rand(0,9)));
            }
                $manager->flush();
    }

    public function getDependencies()
    {
        return [SeasonFixtures::class];
    }
}
