<?php


namespace App\DataFixtures;

use App\Entity\Program;
use App\Service\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    const PROGRAMS = [
        'Walking Dead' => [
            'summary'  => 'Après une apocalypse ayant transformé la quasi-totalité de la population en zombies, un groupe d\'hommes et de femmes mené par l\'officier Rick Grimes tente de survivre...',
            'category' => 'categorie_4',
        ],
        'The Haunting Of Hill House' => [
            'summary'  => 'Plusieurs frères et sœurs qui, enfants, ont grandi dans la demeure qui allait devenir la maison hantée la plus célèbre des États-Unis sont contraints de se retrouver pour faire face à cette tragédie ensemble.',
            'category' => 'categorie_4',
        ],
        'American Horror Story' => [
            'summary'  => 'A chaque saison, son histoire. American Horror Story nous embarque dans des récits à la fois poignants et cauchemardesques, mêlant la peur, le gore et le politiquement correct.',
            'category' => 'categorie_4',
        ],
        'Love Death And Robots' => [
            'summary'  => 'Un yaourt susceptible, des soldats lycanthropes, des robots déchaînés, des monstres-poubelles, des chasseurs de primes cyborgs, des araignées extraterrestres et des démons de l\'enfer assoiffés de sang : tout ce beau monde est réuni dans 18 courts d\'animation déconseillés aux âmes sensibles.',
            'category' => 'categorie_4',
        ],
        'Penny Dreadful' => [
            'summary'  => 'Dans le Londres de l\'époque Victorienne, Vanessa Ives, une jeune femme puissante aux pouvoirs hypnotiques, allie ses forces à celles d\'Ethan, un garçon rebelle et violent aux allures de cowboy, et de Sir Malcolm, un vieil homme riche aux ressources inépuisables.',
            'category' => 'categorie_4',
        ],
        'Fear The Walking Dead' => [
            'summary'  => 'Madison est conseillère d’orientation dans un lycée de Los Angeles. Depuis la mort de son mari, elle élève seule ses deux enfants : Alicia, excellente élève qui découvre les premiers émois amoureux, et son grand frère Nick qui a quitté la fac et cumule les problèmes.',
            'category' => 'categorie_4',
        ],
    ];
    public function load(ObjectManager $manager)
    {
        $i = 0;
        $slugify = new Slugify();
        foreach (self::PROGRAMS as $title => $data) {
            $program = new Program();
            $program->setTitle($title);
            $program->setSummary($data['summary']);
            $slug = $slugify->generate($program->getTitle());
            $program->setSlug($slug);
            $manager->persist($program);
            $this->addReference('program_' . $i, $program);
            $program->setCategory($this->getReference('categorie_4'));
            $i++;
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [CategoryFixtures::class];
    }
}
