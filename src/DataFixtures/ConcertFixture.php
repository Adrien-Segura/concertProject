<?php

namespace App\DataFixtures;

use App\Entity\Concert;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ConcertFixture extends Fixture
{
    public const CONCERT_REFERENCE = 'concert-reference';
    public const CONCERTS = [ "example" , "doctor" , "cakes" , "laborer" , "good-bye" , "hammer" , "destruction" , "bone" , "holiday" , "north" , "religion" , "company" , "trousers" , "drink" , "hospital" , "bedroom" , "curve" , "trick" , "trucks" , "table" , "treatment"];

    public function load(ObjectManager $manager): void
    {
        $i = 0;
        foreach(self::CONCERTS as $c){
            $random = rand();

            $concert = new Concert();
            $concert->setName($c);
            $date = new DateTime();
            $date->setTimestamp(mt_rand(1, 2147385600));
            $concert->setDate($date);
    
            $manager->persist($concert);
            
            $this->addReference(self::CONCERT_REFERENCE."_".$i, $concert);
            $i++;
        }

        $manager->flush();
        
    }

    
}
