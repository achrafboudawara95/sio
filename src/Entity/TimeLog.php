<?php

namespace App\Entity;

use App\Repository\TimeLogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TimeLogRepository::class)]
class TimeLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull(message: "Start time cannot be null.")]
    #[Assert\GreaterThan("now", message: "Start time should be greater than current time.")]
    private ?\DateTimeInterface $startTime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\NotNull(message: "End time cannot be null.")]
    #[Assert\GreaterThan(propertyPath: "startTime", message: "End time should be greater than start time.")]
    private ?\DateTimeInterface $endTime = null;

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: "timeLogs")]
    #[ORM\JoinColumn(nullable: false)]
    private Project $project;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): void
    {
        $this->startTime = $startTime;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(?\DateTimeInterface $endTime): void
    {
        $this->endTime = $endTime;
    }

    public function getProject()
    {
        return $this->project;
    }

    public function setProject(?Project $project): void
    {
        $this->project = $project;
    }
}
