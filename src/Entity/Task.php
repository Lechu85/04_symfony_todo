<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 */
class Task
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $task;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="smallint")
     */
    private $status;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $user;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $deleted;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $prioryty;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $pinned;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $doneAt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $doneByUser;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $remind;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTask(): ?string
    {
        return $this->task;
    }

    public function setTask(string $task): self
    {
        $this->task = $task;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUser(): ?int
    {
        return $this->user;
    }

    public function setUser(?int $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(?bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function getPrioryty(): ?int
    {
        return $this->prioryty;
    }

    public function setPrioryty(?int $prioryty): self
    {
        $this->prioryty = $prioryty;

        return $this;
    }

    public function getPinned(): ?bool
    {
        return $this->pinned;
    }

    public function setPinned(?bool $pinned): self
    {
        $this->pinned = $pinned;

        return $this;
    }

    public function getDoneAt(): ?\DateTimeImmutable
    {
        return $this->doneAt;
    }

    public function setDoneAt(?\DateTimeImmutable $doneAt): self
    {
        $this->doneAt = $doneAt;

        return $this;
    }

    public function getDoneByUser(): ?int
    {
        return $this->doneByUser;
    }

    public function setDoneByUser(?int $doneByUser): self
    {
        $this->doneByUser = $doneByUser;

        return $this;
    }

    public function getRemind(): ?string
    {
        return $this->remind;
    }

    public function setRemind(?string $remind): self
    {
        $this->remind = $remind;

        return $this;
    }
}
