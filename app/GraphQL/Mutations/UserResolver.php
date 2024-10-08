<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\User; // Importoni modelin e User
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

final class UserResolver
{
    /** @param  array<string, mixed>  $args */
    public function addUser(null $_, array $args): User
    {
        // Log the incoming arguments

        $user = new User();
        $user->name = $args['name'];
        $user->email = $args['email'];
        $user->password = Hash::make($args['password']);
        $user->first_name = $args['first_name'];
        $user->last_name = $args['last_name'];
        $user->birthday = $args['birthday'];

        Log::info('Adding user with arguments:', $args);

        return $user;
    }
}
