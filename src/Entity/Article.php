<?php

namespace App\Entity;

use App\Factory\TaggFactory;
use App\Repository\CommentRepository;
use App\Service\UploaderHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 */
class Article
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Tytuł jest wymagany.")
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     * @Gedmo\Slug(fields={"title"})
     */
    private $slug;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $publishedAt;

    /**
     * @ORM\Column(type="integer")
     */
    private $heartCount = 0;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imageFilename;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="article", fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $comments;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Tagg", inversedBy="articles")
     */
    private $tags;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="articles")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull(message="Proszę podać autora artykułu")
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $location;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $specificLocationName;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ArticleReference", mappedBy="article")
     * @ORM\OrderBy({"position"="ASC"})
     */
    private $articleReferences;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->articleReferences = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }


    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function isPublished(): bool
    {
        return $this->publishedAt !== null;
    }

    public function setPublishedAt(?\DateTimeInterface $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getHeartCount(): ?int
    {
        return $this->heartCount;
    }

    public function setHeartCount(int $heartCount): self
    {
        $this->heartCount = $heartCount;

        return $this;
    }

    public function incrementHeartCount(): self
    {
        $this->heartCount = $this->heartCount + 1;

        return $this;
    }

    public function getImageFilename(): ?string
    {
        return $this->imageFilename;
    }

    public function setImageFilename(?string $imageFilename): self
    {
        $this->imageFilename = $imageFilename;

        return $this;
    }

    public function getImagePath()
    {
        return 'imageeeeeellech'; //UploaderHelper::ARTICLE_IMAGE.'/'.$this->getImageFilename();
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getNonDeletedComments(): Collection
    {
        $criteria = CommentRepository::createNonDeletedCriteria();

        return $this->comments->matching($criteria);
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setArticle($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getArticle() === $this) {
                $comment->setArticle(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TaggFactory[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(TaggFactory $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        //info custom validation logic with custom error
        // niestandardowa logika walidacji z niestandardowym błędem

        if (stripos($this->getTitle(), 'the borg') !== false) {
           $context->buildViolation('Wiadomosć z funkcji callback. Do You realy want to yoin the borg?')
            ->atPath('title')
            ->addViolation();
        }
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        //NOTE jeżeli nie podano lokalizacji, albo lokalizacja jest pusta, to definiujemy drugi seter z null.
        if (!$this->location || $this->location === 'interstellar_space') {
            $this->setSpecificLocationName(null);
        }

        return $this;
    }

    public function getSpecificLocationName(): ?string
    {
        return $this->specificLocationName;
    }

    public function setSpecificLocationName(?string $specificLocationName): self
    {
        $this->specificLocationName = $specificLocationName;

        return $this;
    }

    /**
     * @return Collection|ArticleReference[]
     */
    /*
    public function getArticleReferences(): Collection
    {
        return $this->articleReferences;
    }*/
}