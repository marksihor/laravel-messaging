# laravel-messaging
Simple messaging between users.

## Installing

```shell
$ composer require marksihor/laravel-messaging -vvv
```

### Migrations

This step is optional, if you want to customize the tables, you can publish the migration files:

```php
$ php artisan vendor:publish --provider="MarksIhor\\LaravelMessaging\\MessagingServiceProvider" --tag=migrations
```


## Usage

### Use trait on User Model

#### `MarksIhor\LaravelMessaging\Traits\Messageable`

```php
<?php

namespace App\User;

<...>
use MarksIhor\LaravelMessaging\Traits\Messageable;

class User extends Authenticatable
{
    <...>
    use Messageable;
    <...>
}
```

### API

```php

$user()->chats; // get all chats available for given user (with last message)
$user()->chatsUnread; // get all unread chats
$user()->chatsRead; // get all read chats
$user()->chat(1); // get one chat with all messages

$user()->sendMessageToChat(1, ['text' => 'message to chat']); // send message to specified chat (if user is in the chat)
$user()->sendMessageToUser($recipient, ['text' => 'message to user', 'link' => 'https://some.link']); // send message to specified recipient
```

If You need manually to change "read" status, You can do the following:

```php

use MarksIhor\LaravelMessaging\Services\MessagingService;

<...>
MessagingService::markReadForUser($chatId, $userId, $type === 'read' ? 1 : 0)
<...>

```

## License

MIT