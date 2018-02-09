<?php

namespace Vega\Command;

use Vega\Entity\Post;
use Vega\Entity\User;
use Vega\Repository\PostRepository;
use Vega\Repository\UserRepository;
use Vega\Utils\Validator;
use Vega\Entity\Question;
use Vega\Repository\QuestionRepository;
use Vega\Entity\Answer;
use Vega\Repository\AnswerRepository;
use Vega\Entity\Comment;
use Vega\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class VegaUserDeleteCommand extends Command
{
    protected static $defaultName = 'vega:security-delete';

    /** @var SymfonyStyle */
    private $io;
    private $entityManager;
    private $validator;
    private $users;
    private $questions;
    private  $posts;
    private $answers;
    private $comments;

    public function __construct(
        EntityManagerInterface $em,
        Validator $validator,
        UserRepository $users,
        QuestionRepository $questionRepository,
        AnswerRepository $answerRepository,
        CommentRepository $commentRepository,
        PostRepository $postRepository
    ){
        parent::__construct();

        $this->entityManager = $em;
        $this->validator = $validator;
        $this->users = $users;
        $this->questions = $questionRepository;
        $this->answers = $answerRepository;
        $this->comments = $commentRepository;
        $this->posts = $postRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Deletes users from the database')
            ->addArgument('username', InputArgument::REQUIRED, 'The username of an existing security')
            ->setHelp(<<<'HELP'
The <info>%command.name%</info> command deletes users from the database:

  <info>php %command.full_name%</info> <comment>username</comment>

If you omit the argument, the command will ask you to
provide the missing value:

  <info>php %command.full_name%</info>
HELP
            );
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        // SymfonyStyle is an optional feature that Symfony provides so you can
        // apply a consistent look to the commands of your application.
        // See https://symfony.com/doc/current/console/style.html
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (null !== $input->getArgument('username')) {
            return;
        }

        $this->io->title('Delete User Command Interactive Wizard');
        $this->io->text([
            'If you prefer to not use this interactive wizard, provide the',
            'arguments required by this command as follows:',
            '',
            ' $ php bin/console vega:security-delete username',
            '',
            'Now we\'ll ask you for the value of all the missing command arguments.',
            '',
        ]);

        $username = $this->io->ask('Username', null, [$this->validator, 'validateUsername']);
        $input->setArgument('username', $username);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $this->validator->validateUsername($input->getArgument('username'));

        /** @var User $user */
        $user = $this->users->findOneByUsername($username);

        if (null === $user) {
            throw new RuntimeException(sprintf('User with username "%s" not found.', $username));
        }

        // After an entity has been removed its in-memory state is the same
        // as before the removal, except for generated identifiers.
        // See http://docs.doctrine-project.org/en/latest/reference/working-with-objects.html#removing-entities
        $userId = $user->getId();

        $comments = $this->comments->findCommentsByUser($user);
        /** @var Comment $comment */
        foreach ($comments as $comment) {
            if (null != $comment->getQuestion()) {
                /** @var Question $question */
                $question = $this->questions->findOneBy(['id' => $comment->getQuestion()->getId()]);
                $question->removeComment($comment);
            } elseif (null != $comment->getAnswer()) {
                /** @var Answer $answer */
                $answer = $this->answers->findOneBy(['id' => $comment->getAnswer()->getId()]);
                $answer->removeComment($comment);
            } elseif (null != $comment->getPost()) {
                /** @var Post $post */
                $post = $this->posts->findOneBy(['id' => $comment->getPost()->getId()]);
                $post->removeComment($comment);
            }
            
            $this->entityManager->remove($comment);
        }

        $answers = $this->answers->findAnswersByUser($user);
        /** @var Answer $answer */
        foreach ($answers as $answer) {
            /** @var Question $question */
            $question = $this->questions->findOneBy(['id' => $answer->getQuestion()->getId()]);
            $question->removeAnswer($answer);
            $this->entityManager->remove($answer);
        }

        $questions = $this->questions->findQuestionsByUser($user);
        /** @var Question $question */
        foreach ($questions as $question) {
            $this->entityManager->remove($question);
        }

        $posts = $this->posts->findPostsByUser($user);
        /** @var Post $post */
        foreach ($posts as $post) {
            $this->entityManager->remove($post);
        }
        
        $this->entityManager->remove($user);
        dump($this->entityManager);
        $this->entityManager->flush();

        $this->io->success(sprintf('User "%s" (ID: %d, email: %s) was successfully deleted.', $user->getUsername(), $userId, $user->getEmail()));
    }
}
