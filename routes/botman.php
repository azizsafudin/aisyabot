<?php
use App\Http\Controllers\BotManController;
use Mpociot\BotMan\Middleware\Wit;
use Mpociot\BotMan\BotMan;
// Don't use the Facade in here to support the RTM API too :)
$botman = resolve('botman');
$middleware = Wit::create(env('WIT_AI_ACCESS_TOKEN'));

$botman->hears('salam', function(Botman $bot){
    $bot->reply("Wa'alaikumussalam! How can I help you today?");
})->middleware($middleware);

$botman->hears('get_random_number', function(Botman $bot){
    $bot->reply("Sure, is ".rand()." ok?");
})->middleware($middleware);

$botman->hears('who_am_i', function(Botman $bot){
    $user = $bot->userStorage()->get();

    if ($user->has('name')) {
        $bot->reply('You are '.$user->get('name'));
    } else {
        $bot->reply('I do not know you yet.');
    }
})->middleware($middleware);

$botman->hears("my_name_is", function (BotMan $bot) {
    // Store information for the currently logged in user.
    // You can also pass a user-id / key as a second parameter.
    $extras   = $bot->getMessage()->getExtras();
    $entities = $extras['entities'];
    $name = $entities['contact']['value']
    $bot->userStorage()->save([
        'name' => $name
    ]);

    $bot->reply('I will call you '.$name);
})->middleware($middleware);

//for now start_conversation and set_intro does the same thing. Change in the future.
$botman->hears('start_conversation', BotManController::class.'@introConversation')->middleware($middleware);

$botman->hears('set_intro', BotManController::class.'@introConversation')->middleware($middleware);

$botman->fallback(function($bot) {
    $bot->reply("Sorry, I don't quite understand...");
});