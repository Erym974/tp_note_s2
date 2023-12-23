<?php

namespace App\Factory;

use App\Entity\Report;
use App\Repository\ReportRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Report>
 *
 * @method        Report|Proxy create(array|callable $attributes = [])
 * @method static Report|Proxy createOne(array $attributes = [])
 * @method static Report|Proxy find(object|array|mixed $criteria)
 * @method static Report|Proxy findOrCreate(array $attributes)
 * @method static Report|Proxy first(string $sortedField = 'id')
 * @method static Report|Proxy last(string $sortedField = 'id')
 * @method static Report|Proxy random(array $attributes = [])
 * @method static Report|Proxy randomOrCreate(array $attributes = [])
 * @method static ReportRepository|RepositoryProxy repository()
 * @method static Report[]|Proxy[] all()
 * @method static Report[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Report[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Report[]|Proxy[] findBy(array $attributes)
 * @method static Report[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Report[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class ReportFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'author' => UserFactory::random(),
            'reason' => self::faker()->text(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Report $report): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Report::class;
    }
}
