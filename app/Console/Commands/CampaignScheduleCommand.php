<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\NotificationController;
use App\Models\Campaign;
use App\Models\Client;
use App\Models\ClientShop;
use App\Models\Invite;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Console\Command;
use Notify;

class CampaignScheduleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaign:schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return mixed
     */
    public function handle()
    {
        Campaign::where('campaign_start', '<', Carbon::now())
            ->where('campaign_end', '>', Carbon::now())
            ->get()
            ->map(function ($campaign) {
                /** @var Campaign $campaign */
                return $campaign->user->clients()
                    ->where(function ($q) use ($campaign) {
                        /** @var Client $q */
                        // birthday filter
                        if ($campaign->month !== null) {
                            $q->whereMonth('birthday', $campaign->month);
                        }
                        //age filter
                        if ($campaign->age !== null) {
                            $dates = explode('-', $campaign->age);
                            $q->whereBetween('birthday', [
                                Carbon::now()->subYears($dates[1]),
                                Carbon::now()->subYears($dates[0])
                            ]);

                        }
                        // Venue filter
                        if ($campaign->shop_id !== null) {
                            $q->whereHas('invites', function ($q) use ($campaign) {
                                /** @var Invite $q */
                                $q->where('shop_id', $campaign->shop_id);
                            });
                        }
                        // race filter
                        if ($campaign->race !== 'All') {
                            $q->where('race', $campaign->race);
                        }
                    })
//                    ->where(function ($q) use ($campaign) {
//                        /** @var Client $q */
//                        switch ($campaign->customer_type) {
//                            case NotificationController::TWO_VISITS:
//                                $q->whereHas('clientShops', function ($q) {
//                                    /** @var ClientShop $q */
//                                    $q->where('created_at', '>', Carbon::now()->subWeek()->toDateString());
//                                }, '=', 2);
//                                break;
//                            case NotificationController::WEEKEND_PURCHASE:
//                                $q->whereHas('clientShops', function ($q) use ($campaign) {
//                                    /** @var ClientShop $q */
//                                    $q->whereRaw("weekday(created_at) in (5, 6)");
//                                });
//                                break;
//                            case NotificationController::NO_TRANSACTIONS_6:
//                                $q->whereHas('clientShops', function ($q) {
//                                    /** @var ClientShop $q */
//                                    $q->where('created_at', '<', Carbon::now()->subMonths(6)->toDateTimeString());
//                                }, '=', 0);
//                                break;
//                        }
//                    })
                    ->get()
                    ->map(function ($client) use ($campaign) {
                        /** @var Client $item */
                        if (Carbon::now()->timezone($client->timezone)->startOfHour()->toDateTimeString() == Carbon::parse($campaign->date_time)->startOfHour()->toDateTimeString()) {
                            if ($campaign->date_time <= Carbon::now()->toDateTimeString()
                                && $campaign->date_time > Carbon::now()->subMinutes(10)->toDateTimeString()) {
                                $item->devices->map(function ($item) use ($campaign) {
                                    Notify::sendNotification($item->token, 'NextCard', $campaign->text);
                                });
                            }
                        }
                    });
            });
    }
}
