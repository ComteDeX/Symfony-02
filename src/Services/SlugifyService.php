<?php


namespace App\Services;


class SlugifyService
{
    public function unslugify($slug):string
    {
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );
        return $slug;
    }

    public function slugify(string $title) :string
    {
        $slug = preg_replace(
            '/ /',
            '-', strtolower(trim(strip_tags($title)))
        );

        return $slug;
    }

    public function multiSlugify($programs) :array
    {
        $slugs=[];
/*        foreach ($programs as $key => $value) {
            $slugs[$key] = preg_replace(
                '/ /',
                '-', strtolower(trim(strip_tags($value->getTitle())))
            );
        }*/
        foreach ($programs as $key => $value) {
            $slugs[$key] = $this->slugify($value->getTitle());
        }
        return $slugs;
    }

}