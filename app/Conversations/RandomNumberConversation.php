<?php

namespace App\Conversations;

use Illuminate\Foundation\Inspiring;
use Mpociot\BotMan\Answer;
use Mpociot\BotMan\Conversation;
use Mpociot\BotMan\Question;
use Mpociot\Botman\Button;

class RandomNumberConversation extends Conversation
{

    protected $answer;

    protected $intro;
    /**
     * First question
     */
    public function giveNumber()
    {
        $rand = rand();
        $question = Question::create('Sure, is '.$rand.' ok?')
            ->fallback('Unable to ask question')
            ->callbackId('number_ok')
            ->addButtons([
            Button::create('Yes.')->value('yes'),
            Button::create('Give me another one.')->value('no'),
        ]);
        return $this->ask($question, function (Answer $answer) {

         if($answer->isInteractiveMessageReply()){
             $this->answer = $answer->getValue();
             if($this->answer == 'yes'){
                 $this->askNumber();
             }
             else {
                 $this->say('Glad to be of service. :)');
             }
         }
         else{
             $this->answer = $answer->getText();
             $this->say("Sorry, I didn't understand that.");
             $this->ask('Would you like another random number?', [
                 [
                     'pattern' => 'yes|yep|y|yup',
                     'callback' => function () {
                         $this->say('Okay - we\'ll keep going.');
                         $this->askNumber();
                     }
                 ],
                 [
                     'pattern' => 'nah|no|nope|n|nop',
                     'callback' => function () {
                         $this->say('Awesome, glad to be able to help!');
                     }
                 ]
             ]);
         }
        });
    }
    public function askNumber(){
        $rand = rand();
        $question = Question::create('How about '.$rand.'?')
            ->fallback('Unable to ask question')
            ->callbackId('number_ok')
            ->addButtons([
                Button::create('Yes, that\'s good.')->value('yes'),
                Button::create('Give me another one.')->value('no'),
            ]);

        return $this->ask($question, function (Answer $answer) {

            if($answer->isInteractiveMessageReply()){
                $this->answer = $answer->getValue();
                if($this->answer == 'yes'){
                    $this->askNumber();
                }
                else {
                    $this->say('Glad to be of service. :)');
                }
            }
            else{
                $this->answer = $answer->getText();
                $this->say("Sorry, I didn't quite catch that.");
                $this->ask('Would you like another random number?', [
                    [
                        'pattern' => 'yes|yep|y|yup',
                        'callback' => function () {
                            $this->say('Okay - we\'ll keep going.');
                            $this->askNumber();
                        }
                    ],
                    [
                        'pattern' => 'nah|no|nope|n|nop',
                        'callback' => function () {
                            $this->say('Awesome, glad to be able to help!');
                        }
                    ]
                ]);
            }
        });
    }


    /**
     * Start the conversation
     */
    public function run()
    {
        $this->giveNumber();
    }

}