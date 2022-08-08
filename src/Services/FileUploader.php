<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    public function upload(UploadedFile $file, $dir)
    {
        $filename = $file->getClientOriginalName();
        try {
            $file->move(
                $dir,
                $filename,
            );
        } catch (FileException $e) {
            return $e->getMessage();
        }

        return $filename;
    }
}