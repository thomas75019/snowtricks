<?php

namespace App\Service\Handler;
use App\Entity\Trick;

/**
 * Class VideoHandler
 *
 * Handle Videos
 *
 * @package App\Service\Handler
 */
class VideoHandler
{
    /**
     * @param Trick $trick Trick entity
     *
     * @return void
     */
    public function addVideos(Trick $trick) : void
    {
        foreach ($trick->getVideos() as $video) {
            $video->setTrick($trick);
        }

        return;
    }

}