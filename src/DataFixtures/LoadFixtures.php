<?php
 
namespace App\DataFixtures;
 
use Faker\Factory;
use App\Entity\User;
use App\Entity\Trick;
use App\Entity\Comment;
use App\Entity\GroupTrick;
use App\Security\TokenSecurity;
use Psr\Container\ContainerInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Filesystem\Filesystem;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LoadFixtures extends Fixture
{

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var TokenSecurity
     */
    private $tokenSecurity;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Container
     */
    private $container;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        TokenSecurity $tokenSecurity,
        Filesystem $filesystem,
        ContainerInterface $container

    )
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenSecurity = $tokenSecurity;
        $this->filesystem = $filesystem;
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');


        $this->filesystem->remove([$this->container->getParameter('avatars_directory')]);
        $this->filesystem->mkdir($this->container->getParameter('avatars_directory'));

        for($i = 1; $i <= 30; $i++){
            $user = new User;
            $user->setUsername($faker->firstName())
                 ->setEmail($faker->email())
                 ->setPassword($this->passwordEncoder->encodePassword($user, $faker->password()))
                 ->setActive($active = (bool)random_int(0, 1))
                 ->setActiveToken(($active == false) ? $this->tokenSecurity->generateToken() : null)
                 ->setPicturePath(null)
            ;
        
            $manager->persist($user);
            $users[] = $user;
        }

        for($i = 1; $i <= 10; $i++){
            $group = new GroupTrick;
            $group->setName($faker->sentence($nbWords = random_int(1,4), $variableNbWords = true));

            $manager->persist($group);
            $groups[] = $group;
        }

        for($i = 1; $i <= 20; $i++){
            $trick = new Trick;
            $trick->setTitle($faker->text($maxNbChars = random_int(5,100)))
                  ->setSlug($trick->getTitle())
                  ->setDescription($faker->text($maxNbChars = random_int(10,500)))
                  ->setCreatedAt($faker->dateTimeBetween($startDate = '-10 days', $endDate = 'now'))
                  ->setModifiedAt($faker->dateTimeBetween($startDate = $trick->getCreatedAt(), $endDate = 'now'))
                  ->setGroupTrick($faker->randomElement($groups))
            ;
            
            $manager->persist($trick);
            $tricks[] = $trick;
        }

        for($i = 1; $i <= 100; $i++){
            $comment = new Comment;
            $comment->setContent($faker->text($maxNbChars = random_int(10,200)))
                    ->setTrick($trick = $faker->randomElement($tricks))
                    ->setCreatedAt($faker->dateTimeBetween($startDate = $trick->getCreatedAt(), $endDate = 'now'))
                    ->setUser($faker->randomElement($users))                  
            ;
            $manager->persist($comment);
        }

        $manager->flush();
        
    }

}