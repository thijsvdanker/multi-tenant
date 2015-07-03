<?php namespace HynMe\MultiTenant\Tests;

use HynMe\Framework\Testing\TestCase;

class TenancySetupTest extends TestCase
{

    public function testPackages()
    {
        $this->assertTrue(class_exists('HynMe\Framework\FrameworkServiceProvider'), 'Class FrameworkServiceProvider does not exist');
        $this->assertNotFalse($this->app->make('hyn.package.multi-tenant'), 'packages are not loaded through FrameworkServiceProvider');
    }

    /**
     * @depends testPackages
     */
    public function testCommand()
    {
        exec(sprintf('cd %s; sudo php artisan multi-tenant:setup --tenant=%s --email=%s --hostname=%s --webserver=%s',
            base_path(),
            'example',
            'info@example.org',
            'example.org',
            'no')
        );

    }

    /**
     * @depends testCommand
     */
    public function testTenantExistence()
    {
        /** @var \HynMe\MultiTenant\Contracts\TenantRepositoryContract tenant */
        $this->tenant = $this->app->make('HynMe\MultiTenant\Contracts\TenantRepositoryContract');
        /** @var \HynMe\MultiTenant\Models\Tenant|null $tenant */
        $tenant = $this->tenant->findByName('example');

        $this->assertNotNull($tenant, 'Tenant from command has not been created');
    }

    /**
     * @depends testTenantExistence
     */
    public function testHostnameExistence()
    {
        /** @var \HynMe\MultiTenant\Contracts\HostnameRepositoryContract hostname */
        $this->hostname = $this->app->make('HynMe\MultiTenant\Contracts\HostnameRepositoryContract');

        /** @var \HynMe\MultiTenant\Models\Hostname|null $hostname */
        $hostname = $this->hostname->findByHostname('example.org');

        $this->assertNotNull($hostname, 'Hostname from command has not been created');

    }

    public function tearDown()
    {
        if($this->app) {
            /** @var \HynMe\MultiTenant\Contracts\TenantRepositoryContract $tenantRepository */
            $this->tenant = $this->app->make('HynMe\MultiTenant\Contracts\TenantRepositoryContract');
            $this->tenant->forceDeleteByName('example');
        }

        parent::tearDown();
    }
}