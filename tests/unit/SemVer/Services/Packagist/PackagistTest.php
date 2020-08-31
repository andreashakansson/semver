<?php

namespace Semver\Unit\Services\Packagist;

use Composer\Package\Version\VersionParser;
use Mockery;
use PHPUnit\Framework\TestCase;
use Semver\Contracts\Repositories\PackageVersionsRepository;
use Semver\Services\Packagist\Packagist;
use Semver\Unit\Stubs\BuildVersions;

class PackagistTest extends TestCase
{
    use BuildVersions;

    /**
     * @var PackageVersionsRepository|Mockery\Mock
     */
    private $repository;

    /**
     * @var Packagist
     */
    private $service;

    /**
     * Set up.
     */
    public function setUp() :void
    {
        $this->repository = Mockery::mock(PackageVersionsRepository::class);

        $this->service = new Packagist(
            new VersionParser(),
            $this->repository
        );
    }

    /**
     * Tear down.
     */
    public function tearDown() :void
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function it_gets_matched_versions()
    {
        $vendor = 'madewithlove';
        $package = 'elasticsearcher';
        $packageName = "{$vendor}/{$package}";
        $versions = $this->buildVersions();

        $this->repository->shouldReceive('getVersions')
            ->with($packageName)
            ->andReturn($versions);

        $result = $this->service->getMatchingVersions($vendor, $package, '>=0.2.2');

        $this->assertCount(1, $result);
        $this->assertEquals('0.2.2', $result[0]);
    }

    /**
     * @test
     */
    public function it_gets_abandoned_info()
    {
        $vendor = 'phpunit';
        $package = 'dbunit';
        $packageName = "{$vendor}/{$package}";
        $versions = $this->buildVersionsAbandoned();

        $this->repository->shouldReceive('getVersions')
            ->with($packageName)
            ->andReturn($versions);

        $result = $this->service->getAbandonedInfo($vendor, $package);

        $this->assertTrue($result['abandoned']);
        $this->assertEquals('', $result['replacementPackage']);
    }
}
