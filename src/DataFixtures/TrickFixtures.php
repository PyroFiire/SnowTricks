<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use App\Entity\GroupTrick;
use App\Entity\Trick;

class TrickFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i=1; $i < 5 ; $i++) {
            $groupTrick = new GroupTrick();
            $groupTrick->setName("groupe".$i);

            $manager->persist($groupTrick);
            
            for ($j=1; $j < random_int(1,10) ; $j++) { 
                $trick = new Trick();
                $trick->setTitle("Titre du Trick numéro ".$j)
                     ->setDescription("Bonjour, voici la description de l'article numéro ".$j)
                     ->setCreatedAt(new \DateTime())
                     ->setModifiedAt(new \DateTime())
                     ->setGroupTrick($groupTrick);
    
                $manager->persist($trick);
            }
        }
        $manager->flush();
    }
}
