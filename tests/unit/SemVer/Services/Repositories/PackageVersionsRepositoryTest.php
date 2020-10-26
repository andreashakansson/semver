<?php

namespace Semver\Unit\Services\Repositories;

use Mockery;
use Packagist\Api\Client;
use Packagist\Api\Result\Package;
use Packagist\Api\Result\Package\Version;
use PHPUnit\Framework\TestCase;
use Semver\Services\Repositories\PackageVersionsRepository;
use Semver\Unit\Stubs\BuildVersions;

class PackageVersionsRepositoryTest extends TestCase
{
    use BuildVersions;

    /**
     * Tear down.
     */
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function it_calls_packagist_api()
    {
        // Arrange
        $packagistClient = Mockery::mock(Client::class);
        $packages = Mockery::mock(Package::class);
        $repository = new PackageVersionsRepository($packagistClient);
        $packageName = 'madewithlove/elasticsearcher';

        $packages->shouldReceive('getVersions')
            ->once()
            ->andReturn($this->buildVersions());

        $packagistClient->shouldReceive('get')
            ->with($packageName)
            ->once()
            ->andReturn($packages);

        // Act
        $result = $repository->getVersions($packageName);

        // Assert
        $this->assertCount(2, $result);
        $this->assertEquals(
            ['0.2.1', '0.2.2'],
            array_map(function (Version $version) {
                return $version->getVersion();
            }, $result)
        );
    }
}
