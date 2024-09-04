<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Contract\Firestore;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;

class SendUserNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-user-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send user a notification to drink when they havent drink in the last 1 hour';
    protected $messaging;
    protected $auth;
    protected $db;

    public function __construct(Messaging $messaging, Auth $auth, Firestore $firestore){
        parent::__construct();

        $this->messaging = $messaging;
        $this->auth = $auth;
        $this->db = $firestore->database();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userCol = $this->db->collection('user');
        $users = $userCol->documents();

        $year = date('Y');
        $month = date('n');
        $date = date('j');

        foreach ($users as $user) {
            $userId = $user->id();
            $drinkHistory = $userCol->document($userId)
                ->collection('drinkHistory')
                ->document($year)
                ->collection($month)
                ->document($date);

            $drinkData = $drinkHistory->snapshot()->data();

            if ($drinkData && isset($drinkData['lastDrink'])) {
                $lastDrinkTime = Carbon::parse($drinkData['lastDrink']);
                $currentTime = Carbon::now();

                if ($currentTime->diffInMinutes($lastDrinkTime) >= 60) {
                    // Send notification to the user
                    $this->sendNotification($userId);
                }
            }
        }

        return 0;
    }

    protected function sendNotification($userId)
    {
        $userDoc = $this->db->collection('user')->document($userId);
        $user = $userDoc->snapshot()->data();

        if ($user && isset($user['deviceToken'])) {
            $message = CloudMessage::withTarget('token', $user['deviceToken'])
                ->withNotification([
                    'title' => 'Time to Drink Water!',
                    'body' => 'You haven\'t had a drink in the last hour. Stay hydrated!',
                ]);

            $this->messaging->send($message);
        }
    }
}
