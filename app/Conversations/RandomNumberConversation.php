<?php

namespace App\Conversations;

use Mpociot\BotMan\Answer;
use Mpociot\BotMan\Conversation;
use Mpociot\BotMan\Question;

class RandomNumberConversation extends Conversation
{

    protected $answer;

    protected $intro;
    /**
     * First question
     */
    public function giveNumber()
    {
        $rand = rand(0, 9999);
        return $this->ask('Sure, is '.$rand.' ok?', [
            [
                'pattern' => 'nah|no|nope|n|nop',
                'callback' => function () {
                    $this->say('Okay - we\'ll keep going.');
                    $this->askNumber();
                }
            ],
            [
                'pattern' => 'yes|yep|y|yup',
                'callback' => function () {
                    $this->say('Awesome, glad to be able to help!');
                }
            ],
            [
                'pattern' => '.*',
                'callback' => function () {
                    $this->say('I don\'t quite understand, is that a yes or no?');
                }
            ]
        ]);
    }
    public function askNumber(){
        $rand = rand(0, 9999);
        return $this->ask('What about, '.$rand.'?', [
            [
                'pattern' => 'nah|no|nope|n|nop',
                'callback' => function () {
                    $this->say('Okay - we\'ll keep going.');
                    $this->askNumber();
                }
            ],
            [
                'pattern' => 'yes|yep|y|yup',
                'callback' => function () {
                    $this->say('Nice, glad to be of service!');
                }
            ],
            [
                'pattern' => '.*',
                'callback' => function () {
                    $this->repeat('Sorry, I don\'t understand, is that a yes or no?');
                }
            ]
        ]);
    }


    /**
     * Start the conversation
     */
    public function run()
    {
        $this->giveNumber();
    }

}