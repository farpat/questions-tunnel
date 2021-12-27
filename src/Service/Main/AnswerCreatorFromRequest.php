<?php

namespace App\Service\Main;

use App\Entity\Answer;
use Symfony\Component\HttpFoundation\Request;

class AnswerCreatorFromRequest
{
    public function handle(Request $request): Answer
    {
        $answer = new Answer;
        $answer->setIpAddress($request->getClientIp());

        /** @var array<string, string> $answersInRequest */
        $answersInRequest = $request->request->all('answers');

        foreach ($answersInRequest as $questionInString => $answerInString) {
            $answer->addData($questionInString, $answerInString);
        }

        return $answer;
    }
}
