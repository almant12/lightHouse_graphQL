<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\User as ModelsUser;

final readonly class GetUser
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        return ModelsUser::find($args['id']);
    }
}
