<?php
/**
 * Created by PhpStorm.
 * User: tranthaison
 * Date: 11/22/17
 * Time: 4:44 PM
 */

namespace AppBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Oneup\UploaderBundle\Event\PostPersistEvent;

class UploadListener
{
    /**
     * @var ObjectManager
     */
    private $om;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    public function onUpload(PostPersistEvent $event)
    {
        $file = $event->getFile();
        $response = $event->getResponse();

        $url = "";
        $deleteUrl = "";
        $response['success'] = true;
        $response['files'] = [
            'name' => $file->getFilename(),
            'size' => $file->getSize(),
            'url' => $url,
            'deleteUrl' => $deleteUrl,
            'deleteType' => 'DELETE'
        ];

        return $response;
    }
}