<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\DataFixtures\ConcertFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class AppFixtures extends Fixture implements DependentFixtureInterface {
    public function load(ObjectManager $manager): void {
        for ($i = 0; $i < 20; $i++) {

            $concert = $this->getReference(ConcertFixture::CONCERT_REFERENCE."_".$i);
            $room = $this->getReference(RoomFixture::ROOM_REFERENCE."_".$i);

            $concert->setRoom($room);
            
            if (round(rand(0, 1)) == 1) {
                $band = $this->getReference(BandFixture::BAND_REFERENCE."_".$i);
                $rand = rand(2, 6);
                for ($j = 0; $j < $rand; $j++) {
                    $member = $this->getReference(MembersFixture::MEMBERS_REFERENCE."_".($i+$j));
                    $member->setBand($band);
                    $band->addMember($member);
                }
                $concert->addBand($band);
            } else {
                
                $member = $this->getReference(MemberFixture::MEMBER_REFERENCE."_".$i);
                
                $concert->addMember($member);
            }
            $manager->persist($concert);
            
        }



        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            RoomFixture::class,
            ConcertFixture::class,
            BandFixture::class,
            MemberFixture::class,
            MembersFixture::class
        ];
    }
}
