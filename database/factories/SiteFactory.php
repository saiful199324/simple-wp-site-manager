<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Site>
 */
class SiteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
         // fake domain for the site
        $domain = $this->faker->unique()->domainName();

        // container name based on domain (like wp_example_com)
        $containerName = 'wp_' . str_replace('.', '_', $domain);

        // simple db name slug
        $dbName = 'wp_' . preg_replace('/[^a-z0-9_]/', '_', $this->faker->word());

        $monitorToken = bin2hex(random_bytes(16));

        return [
            'name'           => ucfirst($this->faker->words(2, true)) . ' Site',
            'domain'         => $domain,

            // remote server info (you can change to your DO IP if you want)
            'server_host'    => '167.172.86.3',   // or any test IP
            'server_port'    => 22,
            'server_user'    => 'root',

            // database info (password will be encrypted by the cast on the model)
            'db_name'        => $dbName,
            'db_user'        => 'wp_user',
            'db_password'    => 'secret123',

            // docker container
            'container_name' => $containerName,
            'http_port'      => $this->faker->numberBetween(8080, 9000),

            // random status for demo
            'status'         => $this->faker->randomElement([
                'running',
                'stopped',
                'deploying',
                'failed',
            ]),
            'monitor_token'  => $monitorToken,
            'last_reported_at' => now(),
        ];
    }
}
