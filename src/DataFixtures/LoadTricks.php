<?php
 
namespace App\DataFixtures;
 
use Faker\Factory;
use App\Entity\User;
use App\Entity\Trick;
use App\Entity\Video;
use App\Entity\Comment;
use App\Entity\Picture;
use App\Entity\GroupTrick;
use App\Security\TokenSecurity;
use Psr\Container\ContainerInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Filesystem\Filesystem;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LoadTricks extends Fixture implements FixtureGroupInterface
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

    /**
     * @var ObjectManager
     */
    private $manager;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        TokenSecurity $tokenSecurity,
        Filesystem $filesystem,
        ContainerInterface $container,
        ObjectManager $manager

    )
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenSecurity = $tokenSecurity;
        $this->filesystem = $filesystem;
        $this->container = $container;
        $this->manager = $manager;
    }

    public static function getGroups(): array
    {
        return ['start'];
    }

    public function load(ObjectManager $manager)
    {
        $this->filesystem->remove([$this->container->getParameter('avatars_directory')]);
        $this->filesystem->mkdir($this->container->getParameter('avatars_directory'));
        $this->filesystem->remove([$this->container->getParameter('medias_directory')]);
        $this->filesystem->mkdir($this->container->getParameter('medias_directory'));

        //copie mediasStarter in medias, 
        // !!! Don't delete mediasStarter for restart if needed !!!
        $this->filesystem->mirror($this->container->getParameter('pictures_directory').'mediasStarter', $this->container->getParameter('medias_directory'));

        //create Users
        $user = new User;
        $user->setUsername('admin')
                ->setEmail('admin@admin.com')
                ->setPassword($this->passwordEncoder->encodePassword($user, 'adminadmin'))
                ->setActive(true)
        ;
        $manager->persist($user);

        //create Groups
        $groupsName = [
            'Grabs',
            'Rotations',
            'Flips',
            'Offset rotations',
            'Slides',
            'One foot tricks',
            'Old school',
        ];
        for($i = 0; $i <= 6; $i++){
            $group = new GroupTrick;
            $group->setName($groupsName[$i]);
            $manager->persist($group);
            $groups[] = $group;
        }

        // CREATE TRICKS

        // Backside Air
        $trick = new Trick;
        $trick->setTitle('Backside Air')
                ->setSlug($trick->getTitle())
                ->setDescription('The backside air is quite possibly the best trick in snowboarding’s increasingly complex lexicon. Ironically, it’s also one of the easiest to do; however there’s doing it, and there’s doing it well – anyone who’s gotten to grips with turning and popping an ollie will have no problem jumping, slapping the hand on the heel edge, and pulling their legs up, but to do it at speed, with height and get that timeless contortion of body positioning just right… it’s a thing of beauty. Because we love a good tweaked, backside-grabbed aerial perhaps more than is strictly healthy, we also included a couple of excellent examples of straight jump Methods. Though technically you could argue that they’re not backside airs as there’s no backside wall or rotation, they’re sick, memorable moments of snowboarding wrapped up in a similar, aesthetically arousing body movement. Plus we’re always hearing how people would ‘rather see a big Method’ than the progressive pentacorking, so forgive us for taking a liberty and going all in.')
                ->setCreatedAt(new \DateTime)
                ->setGroupTrick($groups[6])
                ->setSpotlightPicturePath('backsideAir.jpg');
        ;
        $this->addVideo($trick, '_CN_yyEn78M');
        $this->addPicture($trick, 'backsideAir1.jpg');
        $manager->persist($trick);

        // Method Air
        $trick = new Trick;
        $trick->setTitle('Method Air')
                ->setSlug($trick->getTitle())
                ->setDescription('It’s one of the most difficult grabs to execute because it involves a combination of different techniques. It’s a Melon grab tweaked up behind your back combined with a Backside Shifty. Start with a Melon grab, which is your front hand on your heel edge between your feet. Now, tweak your Melon grab up behind your back while arching your hips forward. This is the Tourist Method. The key to turning this into a real Method is adding a Backside Shifty. This is the most important part of the trick and the difficult part to execute. Make sure you’ve practiced a lot of Backside Shifties first as they are much easier without a grab.')
                ->setCreatedAt(new \DateTime)
                ->setGroupTrick($groups[6])
                ->setSpotlightPicturePath('methodAir.jpg');
        ;
        $this->addVideo($trick, 'ZWZGE9yp5hA');
        $this->addPicture($trick, 'methodAir1.jpg');
        $this->addPicture($trick, 'methodAir2.jpg');
        $manager->persist($trick);


        // Back Flip
        $trick = new Trick;
        $trick->setTitle('Back Flip')
                ->setSlug($trick->getTitle())
                ->setDescription('Like most inverted tricks, aerial awareness is highly important and getting used to Backflips on a trampoline is a great way to build the correct air awareness. The main difference in learning the Laid-Out Backflip is the arching of your back when initiating the flip. This requires you to push out with your hips as you swing your arms into the flip. As you ride up the take off, your shoulders should be open with your chest pointing forward towards the lip of the jump. Do this early on your approach, opening your shoulders at the last second before you pop can initiate spin as you flip backwards.')
                ->setCreatedAt(new \DateTime)
                ->setGroupTrick($groups[2])
                ->setSpotlightPicturePath('backFlip.jpg');
        ;
        $this->addVideo($trick, '0sehBOkD01Q');
        $this->addPicture($trick, 'backFlip1.jpg');
        $manager->persist($trick);

        // FrontSide 360
        $trick = new Trick;
        $trick->setTitle('FrontSide 360')
                ->setSlug($trick->getTitle())
                ->setDescription('A frontside 360 is when you leave the slope and rotate in the air 360 degrees before hitting the ground again. The "frontside" part of the jump refers to the fact that you turn your chest toward the bottom of the slope first rather than your back. If you snowboard with your left side in front, then you turn counterclockwise; if you snowboard in the "goofy" position with your right side to the front, you turn clockwise. To land the jump, pop off the heel edge of your board into the air and use your arms, head, and torso to gain the momentum you need to rotate in the air. This jump can be difficult to master, so you may want to start with basic jumps, then 180s and 270s.')
                ->setCreatedAt(new \DateTime)
                ->setGroupTrick($groups[1])
                ->setSpotlightPicturePath('frontside360.jpg');
        ;
        $this->addVideo($trick, 'GMFO6frtulE');
        $this->addPicture($trick, 'frontSide3601.jpg');
        $manager->persist($trick);

        // Japan Air
        $trick = new Trick;
        $trick->setTitle('Japan Air')
                ->setSlug($trick->getTitle())
                ->setDescription('Front hand wraps around the front leg and grabs toe edge between the bindings. Knees are bent folding legs backwards toward the board. ')
                ->setCreatedAt(new \DateTime)
                ->setGroupTrick($groups[0])
                ->setSpotlightPicturePath('japanAir.png');
        ;
        $this->addVideo($trick, 'jH76540wSqU');
        $manager->persist($trick);

        // Tail Slide 270 Out
        $trick = new Trick;
        $trick->setTitle('Tail Slide 270 Out')
                ->setSlug($trick->getTitle())
                ->setDescription('A Tail Slide 270 Out is a very common buttering trick that is fun, looks awesome and builds your skills for more advanced jib tricks.')
                ->setCreatedAt(new \DateTime)
                ->setGroupTrick($groups[4])
                ->setSpotlightPicturePath('tailSlide270Out.jpg');
        ;
        $this->addVideo($trick, 'HRNXjMBakwM');
        $manager->persist($trick);

        // 720 Rotation
        $trick = new Trick;
        $trick->setTitle('720 Rotation')
                ->setSlug($trick->getTitle())
                ->setDescription('There’s a trick to doing the 720 and gaining full control of your movement in the air. What you need to do is divide the rotation into several spins and perform them on 2 jumps, one after the other. 1. Imagine yourself performing the rotation. Divide it into two 360s. 2. At the end of the kicker, begin executing the movement, moving your hands simultaneously and turning your head. Spin the first 270 straight, then draw up your legs and turn your shoulders in the direction of rotation, spinning the second 360. 3. Land the trick, cushioning your impact onto the front edge.')
                ->setCreatedAt(new \DateTime)
                ->setGroupTrick($groups[1])
                ->setSpotlightPicturePath('720rotation.jpg');
        ;
        $this->addVideo($trick, '4JfBfQpG77o');
        $this->addVideo($trick, 'K0dx4qT4wrQ');
        $this->addVideo($trick, 'XkkUSEz3I00');
        $manager->persist($trick);

        // Indy Grab
        $trick = new Trick;
        $trick->setTitle('Indy Grab')
                ->setSlug($trick->getTitle())
                ->setDescription('Let your back arm fall between your knees to grasp the approaching board. This is an Indy. Your first few attempts may just be a brief tickle or touch of the board but after some practice you should be able to get a solid hold.')
                ->setCreatedAt(new \DateTime)
                ->setGroupTrick($groups[0])
                ->setSpotlightPicturePath('indyGrab.jpg');
        ;
        $this->addVideo($trick, 'iKkhKekZNQ8');
        $manager->persist($trick);

        // Tamedog
        $trick = new Trick;
        $trick->setTitle('Tamedog')
                ->setSlug($trick->getTitle())
                ->setDescription('The Tamedog is a little intimidating to learn, but quickly becomes one of the most fun tricks to do around the entire mountain. Once you get good at them, it becomes easy and natural to huck Tamedogs off of knuckles, rollers, rails, moguls, jumps, cliffs, and pretty much anything else you can think of. The name Tamedog originates from the opposite of “Wildcat”, which is a straight over the tail backflip. (Learn how to do a Wildcat here). So being the exact opposite, a Tamedog is a straight over the nose frontflip, or sideflip.')
                ->setCreatedAt(new \DateTime)
                ->setGroupTrick($groups[2])
                ->setSpotlightPicturePath('tamedog.jpg');
        ;
        $this->addVideo($trick, 'FMKYKJcUOHk');
        $manager->persist($trick);

        // 180
        $trick = new Trick;
        $trick->setTitle('Frontside 180s')
                ->setSlug($trick->getTitle())
                ->setDescription('Anyone can huck a Frontside 180. They may not land or make it look stylish, but they’ll get it around. This guide will teach you how to stomp a proper Frontside 180 and prevent common problems before they even happen. This guide focuses on two main areas to help you Frontside 180 better; the ideal take off for Frontside 180s and some common problems we see when learning Frontside 180s.')
                ->setCreatedAt(new \DateTime)
                ->setGroupTrick($groups[1])
                ->setSpotlightPicturePath('frontside180s.jpg');
        ;
        $this->addVideo($trick, '-NuJCyR884I');
        $manager->persist($trick);


        $manager->flush();
    }

    private function addVideo(Trick $trick, String $name){
        $video = new Video;
        $video->setTrick($trick);
        $video->setFormat('youtube');
        $video->setName($name);
        $video->setUrlEmbed('https://www.youtube.com/embed/'.$name);
        $this->manager->persist($video);
    }

    private function addPicture(Trick $trick, String $name){
        $picture = new Picture;
        $picture->setTrick($trick);
        $picture->setPath($name);
        $this->manager->persist($picture);
    }


}