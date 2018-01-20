<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Comment;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Query;

/**
 * Answer
 *
 * @ORM\Table(name="answers")
 * @ORM\Entity(repositoryClass="App\Repository\AnswerRepository")
 */
class Answer extends Entity
{
    /**
     * @var string
     * @ORM\Column(name="content", type="text")
     */
    protected $content;

    /**
     * @var boolean
     * @ORM\Column(name="best", type="boolean")
     */
    protected $best;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Question", inversedBy="answers")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $question;

    /**
     * @var Comment[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="answer")
     */
    protected $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return bool
     */
    public function isBest(): bool
    {
        return $this->best;
    }

    /**
     * @param bool $best
     */
    public function setBest(bool $best): void
    {
        $this->best = $best;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return Question
     */
    public function getQuestion(): Question
    {
        return $this->question;
    }

    /**
     * @param Question $question
     */
    public function setQuestion(Question $question): void
    {
        $this->question = $question;
    }

    /**
     * @return Collection
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * @param Comment $comments
     */
    public function addComment(Comment $comment): void
    {
        $comment->setAnswer($this);
        $this->comments->add($comment);
    }

    /**
     * @param Comment $comment
     */
    public function removeComment(Comment $comment): void
    {
        $this->comments->removeElement($comment);
    }
}