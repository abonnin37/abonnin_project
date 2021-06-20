<?php
// api/src/Controller/CreateMediaObjectAction.php

namespace App\Controller;


use App\Entity\Image;
use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CreateMediaObjectAction
{

    public function __invoke(Request $request, EntityManagerInterface $manager): Image
    {
        $uploadedFile = $request->files->get('imageFile');
        if (!$uploadedFile) {
            throw new BadRequestHttpException('"imageFile" is required');
        }

        $project_id = $request->request->get('project_id');
        if (!$project_id) {
            throw new BadRequestHttpException('"project_id" is required');
        }

        $mediaObject = new Image();
        $mediaObject->setImageFile($uploadedFile);
        $mediaObject->setProject($manager->getReference(Project::class, (int)$project_id));
        $mediaObject->setDescription("");


        return $mediaObject;
    }
}