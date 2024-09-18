<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Service to upload an image for our application CodeXpress
 * Image( .jpg, .jpeg, .png, .gif)
 * - Document ( for later )
 *
 * Methods: 
 * uploading a new image 
 * deleting  the old images 
 */
class UploaderService
{
    private $param;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->param = $parameterBag;
    }

    public function uploadImage($file): string
    {
        try {
            /**
             * This original name we can keep it for later if u want to use a portion original name given by the user to our own file name which is being saved to our uploads_images_directory
             */
            //    $orignalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $fileName = uniqid('image-') . '.' . $file->guessExtension();
            $file->move($this->param->get('uploads_images_directory'), $fileName);

            return $this->param->get('uploads_images_directory') . '/' . $fileName;
        } catch (\Exception $e) {
            throw new \Exception('An error occured while uploading the image: ' . $e->getMessage());
        }
    }
}
