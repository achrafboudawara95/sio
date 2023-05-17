<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    public function __construct()
    {
        $this->timeLogs = new ArrayCollection();
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "The name cannot be blank.")]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(targetEntity: TimeLog::class, mappedBy: "project")]
    private $timeLogs;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "projects")]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return Collection|TimeLog[]
     */
    public function getTimeLogs(): Collection
    {
        return $this->timeLogs;
    }

    public function addTimeLog(TimeLog $timeLog): void
    {
        if (!$this->timeLogs->contains($timeLog)) {
            $this->timeLogs[] = $timeLog;
            $timeLog->setProject($this);
        }
    }

    public function removeTimeLog(TimeLog $timeLog): void
    {
        if ($this->timeLogs->contains($timeLog)) {
            $this->timeLogs->removeElement($timeLog);
            // Set the owning side to null (unless already changed)
            if ($timeLog->getProject() === $this) {
                $timeLog->setProject(null);
            }
        }
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }
}
