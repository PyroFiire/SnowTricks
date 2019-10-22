<?php

namespace App\HandleForm;

use App\Entity\Picture;
use App\Repository\TrickRepository;
use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class CreateEditTrickForm
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var TrickRepository
     */
    private $trickRepository;

    /**
     * @var ContainerInterface
     */
    private $container;

    private $videosSite = [
        'youtube' => 'https://www.youtube.com',
        'dailymotion' => 'https://www.dailymotion.com',
    ];

    public function __construct(
        EntityManagerInterface $manager,
        TrickRepository $trickRepository,
        ContainerInterface $container,
        Filesystem $filesystem
        )
    {
        $this->trickRepository = $trickRepository;
        $this->manager = $manager;
        $this->container = $container;
        $this->filesystem = $filesystem;
    }
    public function handleForm($formTrick, $trick) {

        if($formTrick->isSubmitted() && $formTrick->isValid()){

            $trick->setSlug($trick->getTitle());
            if($trick->getCreatedAt()){
                $trick->setModifiedAt(new \DateTime());
            } else {
                $trick->setCreatedAt(new \DateTime());
            }

            $lastSpotlightPicturePath = $trick->getSpotlightPicturePath();

            //remove spotlightPicture (edit Mode)
            if('true' === $formTrick->get('spotlightDelete')->getData()){
                if($lastSpotlightPicturePath !== null){
                    $this->filesystem->remove([$this->container->getParameter('medias_directory').'/'.$lastSpotlightPicturePath]);
                }
                $trick->setSpotlightPicturePath(NULL);
            }

            //Create spotlight picture
            $fileSpotlight = $formTrick->get('spotlightPicturePath')->get('file')->getData();
            if($fileSpotlight !== null) {
                $nameSpotlight = uniqid().'.'. $fileSpotlight->guessExtension();
                $trick->setSpotlightPicturePath($nameSpotlight);

                //move the file and remove the lastFile if exist
                $fileSpotlight->move($this->container->getParameter('medias_directory'), $nameSpotlight );
                if($lastSpotlightPicturePath !== null){
                    $this->filesystem->remove([$this->container->getParameter('medias_directory').'/'.$lastSpotlightPicturePath]);
                }

            }
            $this->manager->persist($trick);

            //Create videos
            foreach ($formTrick->get('videos') as $videoForm) {
                $url = $videoForm->get('url')->getData();
                $video = $videoForm->getData();
                $parseUrl = parse_url($url);
                $host = $parseUrl['scheme'].'://'.$parseUrl['host'];
                parse_str($parseUrl['query'], $parseParams);
                
                if(in_array($host, $this->videosSite, true)) {

                    if('youtube' === $videoSite = array_search($host, $this->videosSite)) {
                        $name = $parseParams['v'];
                        $video->setFormat($videoSite);
                        $video->setName($name);
                        $video->setUrlEmbed($host.'/embed/'.$name);
                    }
                    if('dailymotion' === $videoSite = array_search($host, $this->videosSite)) {
                        $name = substr(strrchr($parseUrl['path'], '/'), 1);
                        $video->setFormat($videoSite);
                        $video->setName($name);
                        $video->setUrlEmbed($host.'/embed/video/'.$name);
                    }
                    $video->setTrick($trick);
                    $this->manager->persist($video);
                }                
            }

            //Create pictures
            foreach ($formTrick->get('pictures') as $pictureForm) {
                $picture = $pictureForm->getData();
                $file = $pictureForm->get('file')->getData();
                $path= uniqid().'.'. $file->guessExtension();

                $picture->setTrick($trick);
                $picture->setPath($path);
                $this->manager->persist($picture);
                $file->move($this->container->getParameter('medias_directory'), $path );
            }

            //remove medias (editMode)
            $mediasWantDelete = explode("/", $formTrick->get('mediaDelete')->getData());
            $currentMedias = $trick->getMedias()->getValues();

            foreach ($currentMedias as $media) {
                if(in_array($media->getId(), $mediasWantDelete)) {
                    if($media instanceof Picture){
                        $this->filesystem->remove([$this->container->getParameter('medias_directory').'/'.$media->getPath()]);
                    }
                    $this->manager->remove($media);
                }
            }

            $this->manager->flush();
            return true;
        }
        
        return false;
    }
}