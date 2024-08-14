<?php

return [
    'minecraft' => [
        'username_url' => 'https://api.mojang.com/users/profiles/minecraft/{username}',
        'id_url' => 'https://sessionserver.mojang.com/session/minecraft/profile/{id}',
        'avatar_url' => 'https://crafatar.com/avatars/{id}',
    ],
    'steam' => [
        'id_url' => 'https://ident.tebex.io/usernameservices/4/username/{id}',
    ],
    'xbl' => [
        'username_url' => 'https://ident.tebex.io/usernameservices/3/username/{username}?type=username',
        'id_url' => 'https://ident.tebex.io/usernameservices/3/username/{id}',
    ],
    'cache_ttl' => 3600,
];
