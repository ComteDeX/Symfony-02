<?php


namespace App\Services;


class SlugifyService
{
    public static function unslugify($slug):string
    {
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );
        return $slug;
    }

    public static function slugify(string $title) :string
    {
        $slug = preg_replace(
            '/ /',
            '-', strtolower(trim(strip_tags($title)))
        );

        return $slug;
    }

    public static function multiSlugify($programs) :array
    {
        $slugs=[];

        foreach ($programs as $key => $value) {
            $slugs[$key] = SlugifyService::slugify($value->getTitle());
        }
        return $slugs;
    }

}
