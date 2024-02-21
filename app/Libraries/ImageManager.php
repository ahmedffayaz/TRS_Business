<?php

namespace App\Libraries;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class ImageManager extends Image
{
    private $directory, $file, $image, $fileName;

    function __construct()
    {
        //
    }

    // Save new image
    public function save()
    {
        $this->image->save(storage_path("app/public/" . $this->getPath()));

        return $this->fileName;
    }

    public function deletePreviousAndSave($deletePath)
    {
        if ($deletePath)
            self::delete($deletePath);

        return $this->save();
    }

    // Make directory
    private function makeDirectory($directory)
    {
        // Make new directory if not exist
        if (!Storage::disk('public')->exists($directory))
            Storage::disk('public')->makeDirectory("$directory");
    }

    // Resize image
    public function resize($width, $height = null)
    {
        $this->image = $this->image->scale($width, $height);

        return $this;
    }

    private function getPath()
    {
        $this->getFileName();
        return "$this->directory/" . $this->fileName;
    }

    public function getFileName()
    {
        $this->fileName = Str::random() . time() . '.' . $this->file->extension();
    }

    public function setFile($file)
    {
        $this->file = $file;
        $this->image = self::read($this->file);
        return $this;
    }


    public function setDirectory($directory)
    {
        if ($directory)
            $this->makeDirectory($directory);

        $this->directory = $directory;
        return $this;
    }

    // Delete image
    public static function delete($path)
    {
        return Storage::disk('public')->delete($path);
    }
}
