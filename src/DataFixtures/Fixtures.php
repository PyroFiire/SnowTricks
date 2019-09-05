<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use App\Entity\GroupTrick;
use App\Entity\Trick;
use App\Entity\Comment;

class Fixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        //Create 4 GroupTrick
        for ($i=1; $i < 5 ; $i++) {
            $groupTrick = new GroupTrick();
            $groupTrick->setName("groupe".$i);

            $manager->persist($groupTrick);

            //Create 1-10 Tricks by GroupTrick
            for ($j=1; $j < random_int(1,10) ; $j++) { 
                $trick = new Trick();
                $trick->setTitle("Trick numéro ".$j." ".$i)
                      ->setSlug(str_replace(" ", "_", $trick->getTitle()))
                      ->setDescription("Bonjour, voici la description de l'article numéro ".$j)
                      ->setCreatedAt(new \DateTime())
                      ->setModifiedAt(new \DateTime())
                      ->setGroupTrick($groupTrick);
    
                $manager->persist($trick);

                //Create 1-10 Comments by Trick
                for ($k=1; $k < random_int(1,10) ; $k++) {
                    $comment = new Comment();
                    $comment->setContent("Je suis un commentaire ".$k)
                            ->setCreatedAt(new \DateTime())
                            ->setTrick($trick);
                
                $manager->persist($comment);
                }
            }
        }
        $manager->flush();
    }
}
