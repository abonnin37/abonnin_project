<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private string $targetDirectory;
    private SluggerInterface $slugger;
    private int $maxSize;

    public function __construct(string $targetDirectory, SluggerInterface $slugger, int $maxSize)
    {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
        $this->maxSize = $maxSize;
    }

    public function upload(UploadedFile $file): string
    {
        if ($file->getSize() > $this->maxSize) {
            throw new FileException('Le fichier est trop volumineux.');
        }

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->targetDirectory, $fileName);
        } catch (FileException $e) {
            throw new FileException('Une erreur est survenue lors de l\'upload du fichier.');
        }

        return $fileName;
    }
} 