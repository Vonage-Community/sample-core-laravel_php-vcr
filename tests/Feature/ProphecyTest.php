<?php

use Laminas\Diactoros\Request;
use Prophecy\Argument;
use Vonage\Client;
use Vonage\Client\APIResource;
use Vonage\SMS\Client as SMSClient;
use Vonage\SMS\ExceptionErrorHandler;
use Vonage\SMS\Message\SMS;

// OK this is how we did it previously
it('will send an sms but will load a manually coded response in with prophecy', function () {
    // ignore the live client because we need to set it up with the mocking library
    // this is a lot of mocking code, plus we don't own any of it but have to test we're sending an SMS?
    $prophetClient = $this->prophesize(Client::class);
    $prophetClient->getRestUrl()->willReturn('https://rest.nexmo.com');

    $api = (new APIResource())
        ->setCollectionName('messages')
        ->setIsHAL(false)
        ->setErrorsOn200(true)
        ->setClient($prophetClient->reveal())
        ->setExceptionErrorHandler(new ExceptionErrorHandler())
        ->setBaseUrl('https://rest.nexmo.com');

    $smsClient = new SMSClient($api);

    $args = [
        'from' => config('vonage.fromNumber'),
        'to' => config('vonage.toNumber'),
        'text' => 'Hello from Vonage!',
        'account-ref' => 'customer1234',
        'client-ref' => 'my-personal-reference'
    ];

    $responsePayload = [
        'to' => config('vonage.toNumber'),
        'message-id' => '15000000751B539A',
        'status' => 0
    ];

    $response = new Laminas\Diactoros\Response(json_encode($responsePayload), 200);

    $prophetClient->send()->willReturn($response);

    $message = (new SMS($args['to'], $args['from'], $args['text']))
        ->setClientRef($args['client-ref'])
        ->setAccountRef($args['account-ref']);

    $response = $smsClient->send($message);
    $lastSms = $response->current();

    // Is the status valid?
    expect($lastSms->getStatus())->toBe(0);

    // Is the messageId correct?
    expect($lastSms->getMessageId())->toBeString();
    expect(strlen($lastSms->getMessageId()))->toBe(16);
})->skip('this does not work despite it being copied from the core codebase');
