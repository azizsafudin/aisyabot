<?php

namespace App\Http\Controllers;
use Mpociot\BotMan\Middleware\Wit;

use App\Conversations\ExampleConversation;
use App\Conversations\Introduction;
use Illuminate\Http\Request;
use Mpociot\BotMan\BotMan;

class BotManController extends Controller
{
	/**
	 * Place your BotMan logic here.
	 */
    public function handle()
    {
    	$botman = app('botman');
        $botman->middleware(Wit::create(env('WIT_AI_ACCESS_TOKEN')));

        $botman->verifyServices(env('TOKEN_VERIFY'));

        // Simple respond method
        $botman->hears('Hello', function (BotMan $bot) {
            $bot->reply('Hi there :)');
        });

        $botman->listen();
        return response()->json(['message' =>'success']);
    }

    /**
     * Loaded through routes/botman.php
     * @param  BotMan $bot
     */
    public function startConversation(BotMan $bot)
    {
        $bot->startConversation(new ExampleConversation());
    }
    /**
     * Loaded through routes/botman.php
     * @param  BotMan $bot
     */
    public function introConversation(BotMan $bot)
    {
        $bot->startConversation(new Introduction());
    }
}
