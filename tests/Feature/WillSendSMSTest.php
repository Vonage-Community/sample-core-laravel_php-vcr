<?php

// This is a live integration test; it sends an SMS
use VCR\VCR;
use Vonage\Client;

it('will send an sms to a live server', function () {
    VCR::turnOn();
    VCR::configure()->setMode('new_episodes');
    VCR::configure()->setCassettePath(base_path() . '/tests/Cassettes');
    VCR::insertCassette('example-test.yml');

    $client = new Client(new Client\Credentials\Basic(
        config('vonage.apiKey'),
        config('vonage.apiSecret'
        )
    ));

    $sms = new Vonage\SMS\Message\SMS(
        config('vonage.toNumber'),
        config('vonage.fromNumber'),
        'Hello from Vonage!'
    );
    $response = $client->sms()->send($sms);
    $lastSms = $response->current();

    // Did we send the message?
    expect($response->count())->toBeOne();

    // Is the status valid?
    expect($lastSms->getStatus())->toBe(0);

    // Is the messageId correct?
    expect($lastSms->getMessageId())->toBeString();
    expect(strlen($lastSms->getMessageId()))->toBe(16);

    VCR::eject();
    VCR::turnOff();
});
