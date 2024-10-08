<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\User;

final readonly class UserQuery
{
    /** @param  array{}  $args */
    public function searchUser(null $_, array $args)
    {

        $query = User::query();

        if (!empty($args['search'])) {
            $query->where('name', 'like', '%' . $args['search'] . '%')
                  ->orWhere('last_name', 'like', '%' . $args['search'] . '%');
        }

        return $query->get();
    }
}
