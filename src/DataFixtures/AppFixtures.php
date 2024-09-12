<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Like;
use App\Entity\Network;
use App\Entity\Note;
use App\Entity\Notification;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    private $slug = null;
    private $hash = null;
    public function __construct(private SluggerInterface $slugger, private UserPasswordHasherInterface $hasher)
    {

        $this->slug = $slugger;
        $this->hash = $hasher;
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $categoryArray = [];

        // Categories
        $categories = [
            'HTML' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/html5/html5-plain.svg',
            'CSS' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/css3/css3-plain.svg',
            'JavaScript' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/javascript/javascript-plain.svg',
            'PHP' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/php/php-plain.svg',
            'SQL' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/postgresql/postgresql-plain.svg',
            'JSON' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/json/json-plain.svg',
            'Python' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/python/python-plain.svg',
            'Ruby' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/ruby/ruby-plain.svg',
            'C++' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/cplusplus/cplusplus-plain.svg',
            'Go' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/go/go-wordmark.svg',
            'bash' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/bash/bash-plain.svg',
            'Markdown' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/markdown/markdown-original.svg',
            'Java' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/java/java-original-wordmark.svg',
        ];
        foreach ($categories as $title => $icon) {
            $category = new Category();
            $category->setTitle($title);
            $category->setIcon($icon);
            $categoryArray[] = $category;
            $manager->persist($category);
        }

        // Users and Notes
        for ($i = 0; $i < 10; $i++) {
            $username = $faker->userName();
            $usernameFinal = $this->slug->slug($username);
            $user = new User();
            $user->setEmail($usernameFinal . '@' . $faker->freeEmailDomain())
                ->setUsername($username)
                ->setPassword($this->hash->hashPassword($user, 'admin'))
                ->setRoles(['ROLE_USER'])
                ->setImage($faker->imageUrl(640, 480, 'animals', true));
            $manager->persist($user);

            // Create Notes for each User
            for ($j = 0; $j < 10; $j++) {
                $note = new Note();
                $note->setTitle($faker->sentence())
                    ->setSlug($this->slug->slug($note->getTitle()))
                    ->setContent($faker->paragraphs(4, true))
                    ->setPublic($faker->boolean(50))
                    ->setViews($faker->numberBetween(100, 10000))
                    ->setCreator($user)
                    ->setCategory($faker->randomElement($categoryArray));
                $manager->persist($note);
            }
        }

        // Persist and Flush Users and Notes
        $manager->flush();

        // Likes
        $users = $manager->getRepository(User::class)->findAll();
        $notes = $manager->getRepository(Note::class)->findAll();

        if (!empty($users) && !empty($notes)) {
            for ($i = 0; $i < 30; $i++) {
                $like = new Like();
                $like->setNote($faker->randomElement($notes));
                $like->setCreator($faker->randomElement($users));
                $manager->persist($like);
            }
        }

        // Networks
        for ($i = 0; $i < 10; $i++) {
            $network = new Network();
            $network->setCreator($faker->randomElement($users));
            $network->setName($faker->company());
            $network->setUrl($faker->url());
            $manager->persist($network);
        }

        //Notifications
        for ($i = 0; $i < 10; $i++) {

            $notification = new Notification();
            $notification->setTitle($faker->emoji())
                ->setType($faker->bothify('?????-#####'))
                ->setArchived($faker->boolean(70))
                ->setContent($faker->sentence())
                ->setNoteId($faker->randomElement($notes));
            $manager->persist($notification);
        }

        $manager->flush();
    }
}
