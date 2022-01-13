<?php

namespace App\DataFixtures;

use App\Entity\Band;
use App\Entity\Picture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BandFixture extends Fixture
{

    public const BAND_REFERENCE = "band-reference";
    public const BANDS = [ "example" , "doctor" , "cakes" , "laborer" , "good-bye" , "hammer" , "destruction" , "bone" , "holiday" , "north" , "religion" , "company" , "trousers" , "drink" , "hospital" , "bedroom" , "curve" , "trick" , "trucks" , "table" , "treatment"];

    public function load(ObjectManager $manager): void
    {
        $i = 0;
        foreach(self::BANDS as $b){
            $picture = new Picture();
            $picture->setUrl("la_photo_du_groupe_".$b.".png")->setName("La photo n°".$b."du groupe")->setAlternativeName("La photo n°".$b." alternative du groupe");
            $band = new Band();
            $band->setName("Groupe ".$b);
            $band->setPicture($picture);
    
            $manager->persist($band);
            
    
            $this->addReference(self::BAND_REFERENCE."_".$i, $band);
            $i++;
        }
        $manager->flush();
    }
}
