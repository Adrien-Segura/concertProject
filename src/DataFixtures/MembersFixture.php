<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Member;

class MembersFixture extends Fixture
{
    public const MEMBERS_REFERENCE = 'members-reference';
    public const MEMBERS = ["Adrian Simpson","Molly Rees","Andrew Hemmings","David Tucker","Connor Avery","Heather Morgan","Sean Harris","Piers Dowd","Virginia Carr","Steven Ogden","Faith Quinn","Adam Graham","Hannah Newman","Ella Hill","Stephanie MacDonald","Joe Vaughan","Vanessa Graham","Simon Johnston","Anthony Cameron","Lily Wilson","Richard Miller","Edward King","Brandon Greene","Una Metcalfe","Bella Hudson","Yvonne Robertson","Jasmine Newman","Molly Mitchell","Ava Skinner","Peter Morgan","Jennifer Parr","Melanie Knox","Lauren Mitchell","Sue Harris","Piers Graham","Brian Alsop","Karen Cornish","Andrea Davidson","Kylie Coleman","David Terry","Heather Peake","Andrew Bond","Amanda Newman","Kimberly Sutherland","Brandon Pullman","Christian Poole","Karen Newman","Molly Poole","Matt Manning","Grace Walker"];

    public function load(ObjectManager $manager): void
    {
        $i = 0;
        foreach(self::MEMBERS as $m){
            $random = rand();
            $member = new Member();
            $member->setName($m);

            $manager->persist($member);
            

            $this->addReference(self::MEMBERS_REFERENCE."_".$i, $member);
            $i++;
        }
        $manager->flush();
    }
}
