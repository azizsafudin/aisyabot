<?php
use App\Http\Controllers\BotManController;
use Mpociot\BotMan\Middleware\Wit;

// Don't use the Facade in here to support the RTM API too :)
$botman = resolve('botman');
$middleware = Wit::create(env('WIT_AI_ACCESS_TOKEN'));

$botman->hears('get_psi', function($bot){
    $latestpsi = $this->getPsiApi();
    $bot->reply('The latest 24 hourly PSI is '.$latestpsi.'.');
});

$botman->hears('salam', function($bot){
    $bot->reply("Wa'alaikumussalam! How can I help you today?");
});
//for now start_conversation and set_intro does the same thing. Change in the future.
$botman->hears('start_conversation', BotManController::class.'@introConversation')->middleware($middleware);

$botman->hears('set_intro', BotManController::class.'@introConversation')->middleware($middleware);

$botman->fallback(function($bot) {
    $bot->reply("Sorry, I don't quite understand...");
});