<?php

namespace App\DataFixtures;

use App\Entity\Serie;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 50; $i++) {
            $user = new User();
            $user
                ->setRoles(['ROLE_USER'])
                ->setEmail($faker->email())
                ->setFirstname($faker->firstName())
                ->setLastname($faker->lastName())
                ->setPassword($this->passwordHasher->hashPassword($user, '123456'));
            $manager->persist($user);
        }

//        for ($i = 0; $i < 50; $i++) {
//            $serie = new Serie();
//            $serie
//                ->setBackdrop('backdrop.png')
//                ->setDateCreated(new \DateTime())
//                ->setFirstAirDate($faker->dateTimeBetween('-3 years'))
//                ->setName($faker->jobTitle())
//                ->setGenres($faker->randomElement(['Fantastique', 'Drama', 'SF']))
//                ->setLastAirDate($faker->dateTimeBetween($serie->getFirstAirDate()))
//                ->setPopularity($faker->numberBetween(0, 9999))
//                ->setPoster('poster.png')
//                ->setStatus($faker->randomElement(['canceled', 'returning', 'ended']))
//                ->setTmdbId($faker->randomNumber(6))
//                ->setVote($faker->numberBetween(0, 10));
//
//            $manager->persist($serie);
//        }

        $manager->flush();
    }
}
