<?php
use App\Http\Controllers\BotManController;
use Mpociot\BotMan\Middleware\Wit;
use Mpociot\BotMan\BotMan;
use App\Conversations\Introduction;
use App\Conversations\RandomNumberConversation;

// Don't use the Facade in here to support the RTM API too :)
$botman = resolve('botman');
$middleware = Wit::create(env('WIT_AI_ACCESS_TOKEN'));

//This block of commands uses NLP

$botman->hears('salam', function(Botman $bot){
    $bot->reply("Wa'alaikumussalam!");
    $name = $bot->userStorage()->get('name');
    if(is_null($name) || isEmpty($name) || !isset($name)) {
        $bot->startConversation(new Introduction());
    }

})->middleware($middleware);

$botman->hears('get_random_number', function(Botman $bot){
    $bot->startConversation(new RandomNumberConversation());
})->middleware($middleware);

$botman->hears('who_am_i', function(Botman $bot){
    $user = $bot->userStorage()->get();
    if ($user->has('name')) {
        $bot->reply('Your name is '.$user->get('name'));
    } else {
        $bot->reply('I do not know you yet.');
        $bot->startConversation(new Introduction());
    }
})->middleware($middleware);

$botman->hears("my_name_is", function (BotMan $bot) {
    $bot->userStorage()->delete('name');

    $extras   = $bot->getMessage()->getExtras();
    $entities = $extras['entities'];
    try {
        $name = $entities['contact'][0]['value'];
        $bot->userStorage()->save([
            'name' => $name,
            'id'   => $bot->getUser()->getId(),
        ]);
    }
    catch(\Exception $e){
        $bot->reply("Sorry I didn't catch your name.");
    }

    $bot->reply('I will call you '.$name);
})->middleware($middleware);


//This block of commands are good old fashioned bot commands

$botman->hears("/forgetme", function(Botman $bot){
    $bot->userStorage()->delete();
    $bot->reply("Ok, I've forgotten everything about you.");
});

$botman->hears("/myid", function(Botman $bot){
    $user = $bot->userStorage()->get();
    try{
        $bot->reply("Your ID is: ". $user->get('id'));
    }
    catch(\Exception $e){
        $bot->reply("Your ID has not been set, tell me your name.");
    }
});



//for now start_conversation and set_intro does the same thing. Change in the future.
$botman->hears('start_conversation', BotManController::class.'@introConversation')->middleware($middleware);

$botman->hears('set_intro', BotManController::class.'@introConversation')->middleware($middleware);

$botman->fallback(function($bot) {
    $bot->reply("Sorry, I don't quite understand...");
});