<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use VCR\VCR;

class RecordTestSuite extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vcr:record {--cassetteName=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Records the output of the test suite';

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
            $this->line('You must give the recording a name');
        }

        VCR::turnOn();
        VCR::configure()->setMode('new_episodes');
        VCR::configure()->setCassettePath(base_path() . '/tests/Cassettes');
        VCR::insertCassette($this->option('cassetteName'));

        $result = exec(base_path() . '/vendor/bin/pest');
        $this->line($result);

        VCR::eject();
        VCR::turnOff();
        return 0;
    }
}
