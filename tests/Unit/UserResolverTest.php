<?php declare(strict_types=1);

namespace Tests\Unit;

use App\GraphQL\Mutations\UserResolver;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class UserResolverTest extends TestCase
{
    use WithFaker; // Përdorimi i trait për të gjeneruar të dhëna të rastësishme, nëse nevojitet

    /** @test */
    public function it_can_add_user_and_log_arguments()
    {
        $userResolver = new UserResolver();

        $args = [
            'name' => 'almant',
            'email' => 'almant123@gmail.com',
        ];
        $result = $userResolver->addUser(null, $args);

        // Verifikoni që rezultati është i njëjtë me argumentet
        $this->assertSame($args, $result);

        // Verifikoni që Log::info u thirr siç pritej
        Log::assertReceived('info', 'Adding user with arguments:');
    }
}
