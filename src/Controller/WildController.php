<?php
// src/Controller/WildController.php
namespace App\Controller;

use App\Entity\Category;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Form\ProgramSearchType;
use App\Services\SlugifyService;
use Doctrine\Migrations\Exception\SkipMigration;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WildController extends AbstractController
{
    /**
     * @Route("/", name="wild_index")
     */
    public function index(SlugifyService $slugifyService, Request $request) : Response
    {

        $form = $this->createForm(
            ProgramSearchType::class,
            null,
            ['method' => Request::METHOD_GET]
        );


        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $data = $form->getData();

            $programs = $this->getDoctrine()
                ->getRepository(Program::class)
                ->findByTitle($data['searchField']);
//                ->findBy(['title' => $data['searchField']]);
        } else {
            $programs = $this->getDoctrine()
                ->getRepository(Program::class)
                ->findAll();
        }

            if (!$programs) {
                throw $this->createNotFoundException(
                    'No program found in program\'s table.'
                );
            }

            $slugs = $slugifyService->multiSlugify($programs);

            return $this->render(
                'wild/index.html.twig', [
                'programs' => $programs,
                'formSearch' => $form->createView(),
//                'formCat' => $formCat->createView(),
                'slugs' => $slugs,
            ]);

    }

    /**
     * Getting a program with a formatted slug for title
     *
     * @param string $slug The slugger
     * @Route("/show/{slug<^[a-z0-9-]+$>}", defaults={"slug" = null}, name="wild_showxx")
     * @return Response
     */
    public function show(?string $slug, SlugifyService $slugifyService):Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }

//        $slug = $this->unslugify($slug);
        $slug = $slugifyService->unslugify($slug);

        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with '.$slug.' title, found in program\'s table.'
            );
        }

        return $this->render('wild/show.html.twig', [
            'program' => $program,
            'slug'  => $slug,
        ]);
    }

     /**
     * Getting all program from a given category
     *
     * @param string $categoryName The Category
     * @Route("wild/category/{categoryName}", name="show_category")
     * @return Response
     */
    public function showByCategory(string $categoryName, SlugifyService $slugifyService)
    {
        if (!$categoryName) {
            throw $this
                ->createNotFoundException('No category has been sent to find a program in program\'s table.');
        }

        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['name' => strtolower($categoryName)]);

        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(
                ['category' => $category->getId()],
                ['id' => 'desc'],
                3,
                0
            );

        if (!$programs) {
            throw $this->createNotFoundException(
                'No program found in program\'s table.'
            );
        }

        $slugs = $slugifyService->multiSlugify($programs);

        return $this->render(
            'wild/category.html.twig', [
            'category' => $category->getName(),
            'programs' => $programs,
            'slugs'     => $slugs,
        ]);
    }

     /**
     * Getting all program from a given category
     *
     * @Route("wild/category/", name="show_allCategories")
     * @return Response
     */
    public function showAllCategories(SlugifyService $slugifyService)
    {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();


//        $slugs = $slugifyService->multiSlugify($programs);

        return $this->render(
            'wild/allCategory.html.twig', [
            'categories' => $category,
        ]);
    }

    /**
     * Getting a program with a formatted slug for title
     *
     * @param string $slug The slugger
     * @Route("/showByProgram/{slug<^[a-z0-9-]+$>}", defaults={"slug" = null}, name="wild_show")
     * @return Response
     */
    public function showByProgram(?string $slug, SlugifyService $slugifyService) :Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }
        $slug = $slugifyService->unslugify($slug);

        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with '.$slug.' title, found in program\'s table.'
            );
        }

        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findBy(
                ['program' => $program->getId()],
                    ['number' => 'ASC']
            );

        return $this->render(
            'wild/showByProgram.html.twig' , [
            'program' => $program,
            'seasons' => $season,
            'slug'  => $slug,
        ]);
    }

    /**
     * Getting a program with a formatted slug for title
     *
     * @param string $slug The slugger
     * @Route("/showBySeason/{id<^[0-9]+$>}", defaults={"id" = null}, name="wild_showBySeason")
     * @return Response
     */
    public function showBySeason(?int $id) :Response
    {
        if (!$id) {
            throw $this
                ->createNotFoundException('No id has been sent to find a season in season\'s table.');
        }

        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findOneBy(['id' => $id]);
        if (!$season) {
            throw $this->createNotFoundException(
                'No season with this id : '.$id.' has been found in season\'s table.'
            );
        }

        $episodes = $this->getDoctrine()
            ->getRepository(Episode::class)
            ->findBy(['season' => $season->getId()]);

        $programId = $season->getProgram()->getId();
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['id' => $programId]);

        return $this->render(
            'wild/showBySeason.html.twig' , [
            'program' => $program,
            'season' => $season,
            'episodes' => $episodes,
        ]);
    }

    /**
     * Getting episodes from a program id
     *
     * @Route("/showEpisode/{id<^[0-9]+$>}", defaults={"id" = null}, name="wild_showEpisodes")
     * @param Episode $episode
     * @return Response
     */
    public function showEpisode(Episode $episode, SlugifyService $slugifyService) : Response
    {
        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findOneBy(['id' => $episode->getSeason()]);

        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['id' => $season->getProgram()]);

        return $this->render('wild/showEpisode.html.twig', [
            'episode'=>$episode,
            'season'=>$season,
            'program' => $program,
            'slug' => $slugifyService->slugify($program->getTitle())
        ]);
    }
}
