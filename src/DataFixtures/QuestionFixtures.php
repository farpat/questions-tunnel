<?php

namespace App\DataFixtures;

use App\Entity\Question;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class QuestionFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $question = $this->makeQuestion("What's your name ?");
        $manager->persist($question);

        $question = $this->makeQuestion("How old are you ?", ['18-25', '25-35', '35-50', '50+']);
        $manager->persist($question);

        $question = $this->makeQuestion("What's your favorite pet", ['cat', 'dog', 'bird', 'racoon']);
        $manager->persist($question);

        $manager->flush();
    }

    /**
     * @param string[] $suggestions
     */
    public function makeQuestion(string $label, array $suggestions = []): Question
    {
        $question = (new Question);
        if (empty($suggestions)) {
            $question->setType(Question::TYPE_FREE_FIELD);
        }
        else {
            $question->setType(Question::TYPE_WITH_SUGGESTIONS);
            foreach ($suggestions as $position => $suggestion) {
                $question->addSuggestion($position, $suggestion);
            }
        }
        $question->setLabel($label);

        return $question;
    }
}
