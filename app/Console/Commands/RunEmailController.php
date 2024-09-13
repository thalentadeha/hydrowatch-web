<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\EmailController;

class RunEmailController extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send drink reminder email to user';

    /**
     * Execute the console command.
     */
    private $emailController;

    public function __construct(
        EmailController $emailController
    ) {
        $this->emailController = $emailController;
        parent::__construct();
    }

    public function handle()
    {
        $this->emailController->sendReminderEmail();
    }
}
