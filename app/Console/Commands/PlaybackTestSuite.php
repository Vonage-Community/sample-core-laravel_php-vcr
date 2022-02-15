<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use VCR\VCR;

class PlaybackTestSuite extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vcr:playback {--cassetteName=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs PEST with chosen VCR cassette';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (!$this->option('cassetteName' )) {
            $this->line('You must specify a cassette');
        }

        VCR::turnOn();
        VCR::configure()->setMode('none');
        VCR::configure()->setCassettePath(base_path() . '/tests/Cassettes');
        VCR::insertCassette($this->option('cassetteName'));

//        $result = exec(base_path() . '/vendor/bin/pest');
        $process = new Process([base_path() . '/vendor/bin/pest']);
//        $this->line($result);
        $process->run();

        VCR::turnOff();
        $this->line($process->getOutput());
        return 0;
    }
}
