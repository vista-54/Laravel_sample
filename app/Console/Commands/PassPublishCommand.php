<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\Device;
use App\Models\Pass;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PassPublishCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pass:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification to clients about pass is publish';

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
        Pass::where('start_date', Carbon::now()->toDateString())
            ->get()
            ->map(function ($item) {
                /** @var Pass $item */
                $item->user->clients->map(function ($q) use ($item) {
                    /** @var Client $q */
                    $q->devices->map(function ($q) use ($item) {
                        /** @var Device $q */
                        \Notification::send($q->token, trans('admin/message.system_offer_notify') . ' ' . $item->name);
                    });
                });
            });
    }
}
