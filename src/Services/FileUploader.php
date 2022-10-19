<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    protected $imageOptimizer;

    public function __construct(ImageOptimizer $imageOptimizer)
    {
        $this->imageOptimizer = $imageOptimizer;
    }

    public function upload(UploadedFile $file, $dir)
    {
        $filename = $file->getClientOriginalName();
        $path_to = $dir."/".$filename;
        try {
            $file->move(
                $dir,
                $filename,
            );
            $this->imageOptimizer->resize($path_to);

        } catch (FileException $e) {
            return $e->getMessage();
        }

        return $filename;
    }
}