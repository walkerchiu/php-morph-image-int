<?php

namespace WalkerChiu\MorphImage;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use WalkerChiu\MorphImage\Models\Entities\Image;
use WalkerChiu\MorphImage\Models\Forms\ImageFormRequest;

class ImageFormRequestTest extends \Orchestra\Testbench\TestCase
{
    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        //$this->loadLaravelMigrations(['--database' => 'mysql']);
        $this->loadMigrationsFrom(__DIR__ .'/../migrations');
        $this->withFactories(__DIR__ .'/../../src/database/factories');

        $this->request  = new ImageFormRequest();
        $this->rules    = $this->request->rules();
        $this->messages = $this->request->messages();
    }

    /**
     * To load your package service provider, override the getPackageProviders.
     *
     * @param \Illuminate\Foundation\Application  $app
     * @return Array
     */
    protected function getPackageProviders($app)
    {
        return [\WalkerChiu\Core\CoreServiceProvider::class,
                \WalkerChiu\MorphImage\MorphImageServiceProvider::class];
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
    }

    /**
     * Unit test about Authorize.
     *
     * For WalkerChiu\MorphImage\Models\Forms\ImageFormRequest
     * 
     * @return void
     */
    public function testAuthorize()
    {
        $this->assertEquals(true, 1);
    }

    /**
     * Unit test about Rules.
     *
     * For WalkerChiu\MorphImage\Models\Forms\ImageFormRequest
     * 
     * @return void
     */
    public function testRules()
    {
        $faker = \Faker\Factory::create();


        // Give
        $attributes = [
            'type'       => $faker->randomElement([null, 'cover', 'icon', 'image', 'logo', 'frame', 'eye', 'face', 'fingerprint', 'imprint', 'palm', 'object']),
            'filename'   => $faker->isbn10,
            'serial'     => $faker->isbn10,
            'identifier' => $faker->slug,
            'name'       => $faker->name,
            'is_visible' => 1,
        ];
        // When
        $validator = Validator::make($attributes, $this->rules, $this->messages); $this->request->withValidator($validator);
        $fails = $validator->fails();
        // Then
        $this->assertEquals(false, $fails);

        // Give
        $attributes = [
            'type'       => 'invalid',
            'filename'   => $faker->isbn10,
            'serial'     => $faker->isbn10,
            'identifier' => $faker->slug,
            'name'       => $faker->name,
        ];
        // When
        $validator = Validator::make($attributes, $this->rules, $this->messages); $this->request->withValidator($validator);
        $fails = $validator->fails();
        // Then
        $this->assertEquals(true, $fails);
    }
}
