<?php
/**
 * Created by PhpStorm.
 * User: heaven
 * Date: 1/16/2018
 * Time: 20:52
 */

namespace App\DataFixtures;

ini_set('memory_limit', -1);

use App\Entity\Answer;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\Question;
use App\Entity\Role;
use App\Entity\Setting;
use App\Entity\Tag;
use App\Entity\User;
use Faker\Factory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AskFixtures extends Fixture
{
    const CATEGORY_NUMS = 10;

    const TAG_NUMS = 20;

    const USER_NUMS = 100;

    const QUESTION_NUMS = 1000;

    const ANSWER_NUMS = 10;

    const POST_NUMS = 100;

    const COMMENT_NUMS = 10;

    private $tagName;

    private $faker;

    private $passwordEncoder;
    
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->faker = Factory::create();
        $this->passwordEncoder = $passwordEncoder;
    }
    
    public function load(ObjectManager $manager)
    {
        //$this->loadCategory($manager);
        $this->loadTag($manager);
        $this->loadSetting($manager);
        $this->loadRole($manager);
        $this->loadUser($manager);
        $this->loadQuestion($manager);
        $this->loadPosts($manager);
    }
    
    public function loadCategory(ObjectManager $manager)
    {
        $name = [];

        foreach (range(1, self::CATEGORY_NUMS) as $i) {
            $category = new Category();

            do {
                $categoryName= $this->faker->citySuffix;
            } while (in_array($categoryName, $name));

            $name[$i] = $categoryName;
            $category->setName($categoryName);
            $category->setGrade(0);
            $category->setDescription($this->faker->sentence);
            $category->setSort(0);
            $this->addReference('category-' . $i, $category);

            $manager->persist($category);
        }

        $manager->flush();
    }

    public function loadTag(ObjectManager $manager)
    {
        $name = [];

        for ($i = 0; $i < self::TAG_NUMS; $i++) {
            $tag = new Tag();

            do {
                $tagName = $this->faker->word;
            } while (in_array($tagName, $name)); //while ($manager->getRepository(Tag::class)->findBy(['name' => $name[$i]]) != null);

            $name[$i] = $tagName;

            $tag->setName($tagName);
            $tag->setDescription($this->faker->paragraph(mt_rand(3, 5)));
            $this->addReference('tag-' . $tagName, $tag);

            $manager->persist($tag);
        }

        $this->tagName = $name;

        $manager->flush();
    }

    public function loadSetting(ObjectManager $manager)
    {
        $settingArrays = $this->getSettings();

        foreach ($settingArrays as $settingArray) {
            $setting = new Setting();
            $setting->setName($settingArray['name']);
            $setting->setValue($settingArray['value']);

            $manager->persist($setting);
        }

        $manager->flush();
    }

    public function loadRole(ObjectManager $manager)
    {
        $roleArrays = $this->getRoles();

        foreach ($roleArrays as $roleArray) {
            $role= new Role();
            $role->setRoles([$roleArray[0]]);
            $role->setDescription($roleArray[1]);
            $this->addReference($roleArray[0], $role);

            $manager->persist($role);
        }

        $manager->flush();
    }

    public function loadUser(ObjectManager $manager)
    {
        $manager->persist(
            $this->setUser(
                'heaven',
                'heaven',
                'heavenwoo@live.com',
                true,
                true
            )
        );

        foreach (range(1, self::USER_NUMS) as $i) {
            $manager->persist($this->setUser('', '', '', '', '', $i));
        }

        $manager->flush();
    }

    private function setUser($username = '', $password = '', $email = '', $enable = false, $supperAdmin = false, $i = 0)
    {
        $user = new User();
        $user->setUsername($username ?: $this->faker->userName);
        $user->setEmail($email ?: $this->faker->email);
        $user->setEnabled($enable ? true : (bool)mt_rand(0, 1));
        $user->setPassword($this->passwordEncoder->encodePassword($user, $password ?: $this->faker->word));
        $user->setRoles($supperAdmin ? $this->getReference('ROLE_SUPPER_ADMIN') : $this->getReference('ROLE_USER'));
        $user->setCreatedAt($this->faker->dateTimeBetween('-1 year', '-10 days'));
        $user->setUpdatedAt($user->getCreatedAt());

        $this->setReference('username-' . $i, $user);

        return $user;
    }

    public function loadQuestion(ObjectManager $manager)
    {
        foreach (range(1, self::QUESTION_NUMS) as $i) {
            $question = new Question();

            $question->setSubject(implode(' ', array_map('ucfirst', $this->faker->words(mt_rand(3, 5)))));
            $question->setContent($this->faker->paragraph(mt_rand(6, 10)));
            $question->setViews(mt_rand(0, 1000));
            $question->setSolved((bool)mt_rand(0, 1));
            $question->setCreatedAt($this->faker->dateTimeBetween('-1 year', '-10 days'));
            $question->setUpdatedAt($question->getCreatedAt());
            $question->setUser($this->getReference('username-' . mt_rand(0, self::USER_NUMS)));
            //$question->setCategory($this->getReference('category-' . mt_rand(1, self::CATEGORY_NUMS)));
            $question->addTags(...$this->getRandomTags());

            $this->addAnswers($manager, $question);

            $this->addComments($manager, $question);

            $manager->persist($question);
        }

        $manager->flush();
    }

    private function addAnswers(ObjectManager $manager, Question $question)
    {
        $answerNums = mt_rand(1, self::ANSWER_NUMS);
        $isBestId = $question->isSolved() ? mt_rand(1, $answerNums) : 0;

        foreach (range(1, $answerNums) as $i) {
            $answer = new Answer();

            $answer->setContent($this->faker->paragraph(mt_rand(1, 3)));
            $answer->setBest($isBestId == $i);
            $answer->setUser($this->getReference('username-' . mt_rand(0, self::USER_NUMS)));
            $answer->setCreatedAt($this->faker->dateTimeBetween($question->getCreatedAt(), 'now'));
            $answer->setUpdatedAt($answer->getCreatedAt());

            $this->addComments($manager, $answer);

            $question->addAnswer($answer);

            $manager->persist($answer);
        }
    }

    private function loadPosts(ObjectManager $manager)
    {
        foreach (range(1, mt_rand(1, self::POST_NUMS)) as $i) {
            $post = new Post();

            $post->setSubject(implode(' ', array_map('ucfirst', $this->faker->words(mt_rand(3, 5)))));
            $post->setContent($this->faker->paragraph(mt_rand(6, 10)));
            $post->setCreatedAt($this->faker->dateTimeBetween('-1 year', '-10 days'));
            $post->setUpdatedAt($post->getCreatedAt());
            $post->setUser($this->getReference('username-' . mt_rand(0, self::USER_NUMS)));
            $post->addTags(...$this->getRandomTags());

            $this->addComments($manager, $post);

            $manager->persist($post);
        }

        $manager->flush();
    }

    private function addComments(ObjectManager $manager, $entity)
    {
        foreach (range(1, mt_rand(1, self::COMMENT_NUMS)) as $i) {
            $comment = new Comment();

            $comment->setContent($this->faker->paragraph(mt_rand(1, 3)));
            $comment->setCreatedAt($this->faker->dateTimeBetween($entity->getCreatedAt(), 'now'));
            $comment->setUpdatedAt($comment->getCreatedAt());
            $comment->setUser($this->getReference('username-' . mt_rand(0, self::USER_NUMS)));

            $entity->addComment($comment);

            $manager->persist($comment);
        }
    }

    private function getRandomTags(): array
    {
        $tagNames = $this->tagName;
        shuffle($tagNames);
        $selectedTags = array_slice($tagNames, 0, mt_rand(2, 5));

        return array_map(function ($tagName) { return $this->getReference('tag-'.$tagName); }, $selectedTags);
    }

    private function getSettings()
    {
        return [
            [
                'name' => 'sitename',
                'value' => 'Taichi Ask'
            ],
            [
                'name' => 'siteurl',
                'value' => 'http://taichi.ask'
            ],
            [
                'name' => 'site_admin_email',
                'value' => 'heavenwoo@live.com'
            ]
        ];
    }

    private function getRoles()
    {
        return [
            ['ROLE_SUPPER_ADMIN', 'Supper admin role'],
            ['ROLE_ADMIN', 'Admin role'],
            ['ROLE_USER', 'User role'],
            ['ROLE_GUEST', 'Guest role']
        ];
    }
}