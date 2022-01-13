<?php

namespace App\DataFixtures;

use App\Entity\Picture;
use App\Entity\Room;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RoomFixture extends Fixture
{
    public const ROOM_REFERENCE = 'room-reference';
    public const ROOMS = [ "giraffe" , "sow" , "battle" , "cover" , "stew" , "macho" , "bike" , "prose" , "tomatoes" , "many" , "mark" , "whisper" , "supply" , "blush" , "thin" , "mow" , "thick" , "injure" , "available" , "square"];

    public function load(ObjectManager $manager): void
    {
        $i = 0;
        foreach(self::ROOMS as $r){
            $picture = new Picture();
            $picture->setUrl("la_photo_de_salle_".$r.".png")->setName("La photo n°".$r."de la salle")->setAlternativeName("La photo n°".$r." alternative de la salle");
    
            $room = new Room;
            $room->setName($r);
            $room->setPlacesNumber(rand(500, 5000));
    
            $room->setPicture($picture);
    
            $manager->persist($room);
           
    
            $this->addReference(self::ROOM_REFERENCE."_".$i, $room);
            $i++;
        }

        $manager->flush();
        

    }
}
