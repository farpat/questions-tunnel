<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
#[ORM\Table(name: "questions")]
class Question
{
    //TYPES
    public const TYPE_FREE_FIELD = 'free_field';
    public const TYPE_WITH_SUGGESTIONS = 'with_suggestions';
    public const TYPES = [
        self::TYPE_FREE_FIELD, self::TYPE_WITH_SUGGESTIONS
    ];

    /**
     * @var array<int, string>
     * La clé représente la position de la suggestion
     * La valeur représente la suggestion
     */
    #[ORM\Column(type: 'json')]
    private array $suggestions = [];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(type: 'text')]
    private string $label = '';

    #[ORM\Column(type: 'string', length: 20)]
    private string $type = self::TYPE_FREE_FIELD;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        if (!in_array($type, self::TYPES)) {
            throw new \InvalidArgumentException("The type << $type >> is not recognized. See Question::TYPES");
        }

        $this->type = $type;

        return $this;
    }

    public function addSuggestion(int $position, string $suggestion): self
    {
        $this->suggestions[$position] = $suggestion;

        return $this;
    }

    /**
     * @return array<int, string>
     */
    public function getSuggestions(): array
    {
        return $this->suggestions;
    }
}
