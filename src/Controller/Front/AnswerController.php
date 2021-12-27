<?php

namespace App\Controller\Front;

use App\Repository\QuestionRepository;
use App\Service\Main\AnswerCreatorFromRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route(name: 'app_front_answer_')]
class AnswerController extends AbstractController
{
    #[Route('/give-an-answer', name: 'index')]
    public function index(QuestionRepository $questionRepository, SerializerInterface $serializer): Response
    {
        return $this->render('front/answer/index.html.twig', [
            'questions_in_json' => $serializer->serialize($questionRepository->findAll(), 'json')
        ]);
    }

    #[Route('/submit-answer', name: 'submit')]
    public function submit(Request $request, AnswerCreatorFromRequest $answerCreator, EntityManagerInterface $entityManager): Response
    {
        try {
            //TODO: add the check of data
            $answer = $answerCreator->handle($request);
            $entityManager->persist($answer);
            $entityManager->flush();
        } catch (\Throwable $throwable) {
            return $this->json([
                'status'  => 'KO',
                'message' => $throwable->getMessage(),
                'data'    => $request->request->all()
            ]);
        }

        $this->addFlash('success', 'Thanks for your submission');

        return $this->json([
            'status'  => 'OK',
            'message' => 'Thanks for your submission',
            'data'    => $request->request->all(),
        ]);
    }
}
