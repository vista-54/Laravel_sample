<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\Device;
use App\Models\Offer;
use Carbon\Carbon;
use Illuminate\Console\Command;

class OfferPublishCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'offer:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification to clients about offer is publish';

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
        Offer::where('start_date', Carbon::now()->toDateString())
            ->get()
            ->map(function ($item) {
                /** @var Offer $item */
                $item->loyaltyProgram->user->clients->map(function ($q) use ($item) {
                    /** @var Client $q */
                    $q->devices->map(function ($q) use ($item) {
                        /** @var Device $q */
                        \Notification::send($q->token, trans('admin/message.system_offer_notify') . ' ' . $item->name);
                    });
                });
            });
    }
}
