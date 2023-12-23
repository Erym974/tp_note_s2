<?php

namespace App\DataFixtures;

use App\Factory\CategoryFactory;
use App\Factory\CommentFactory;
use App\Factory\InvitationFactory;
use App\Factory\PostFactory;
use App\Factory\ReportFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private Generator $faker;

    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {

        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        /* TODO
         * Créer entre 2 et 3 commentaires pour chacun des articles
         */

        // Liste des titres de catégories
        $categories = [
            'Voyage',
            'Cinéma',
            'Musique',
            'Cuisine',
            'Voiture',
            'Divers'
        ];

        CategoryFactory::createMany(count($categories), static function (int $i) use ($categories) {
            return ['name' => $categories[$i - 1]];
        });

        /** Admin */
        $admin = UserFactory::createOne([
            'email' => 'admin@admin.fr',
            'username' => 'admin',
            'password' => 'admin',
            'roles' => ['ROLE_ADMIN']
        ]);

        /** Moderator */
        $moderator = UserFactory::createOne([
            'email' => 'moderator@moderator.fr',
            'username' => 'moderator',
            'password' => 'moderator',
            'roles' => ['ROLE_MODERATOR']
        ]);

        /** User */
        $user = UserFactory::createOne([
            'email' => 'user@user.fr',
            'username' => 'user',
            'password' => 'user'
        ]);

        UserFactory::createMany(15);

        PostFactory::createMany(15, [
            'author' => $admin,
            'comments' => CommentFactory::new()->many(random_int(0, 3)),
        ]);

        PostFactory::createMany(15, [
            'author' => $moderator,
            'comments' => CommentFactory::new()->many(random_int(0, 3)),

        ]);

        PostFactory::createMany(15, [
            'author' => $user,
            'comments' => CommentFactory::new()->many(random_int(0, 3)),
            'reports' => ReportFactory::new()->many(random_int(0, 3)),
        ]);

        PostFactory::createMany(75, [
            'comments' => CommentFactory::new()->many(random_int(2, 3)),
            'reports' => ReportFactory::new()->many(random_int(0, 2)),
        ]);

        InvitationFactory::createMany(10);
    }
}
