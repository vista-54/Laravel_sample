<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//
//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});


use App\Console\Commands\CampaignScheduleCommand;
use App\Http\Controllers\Admin\NotificationController;
use App\Models\Campaign;
use App\Models\Client;
use App\Models\ClientShop;
use App\Models\Device;
use App\Models\Invite;
use App\Models\Offer;
use App\Models\Pass;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Faker\Generator as Faker;

Route::group(['prefix' => 'admin'], function () {

//    Route::post('import', function (Faker\Generator $faker) {
//        /** @var User $merchant */
//        DB::beginTransaction();
//         //create merchant
//        $merchant = factory(User::class)->create([
//            'email' => 'efauvel+bonjour@gmail.com',
//            'password' => Hash::make('BonJour19*45'),
//            'verified' => 1
//        ]);
////        $merchant = User::find(61);
//        $lp = $merchant->loyaltyProgram()->first();
//        $clients = factory(\App\Models\Client::class, 50)->create(['user_id' => $merchant->id]);
//        $offers = factory(Offer::class, 5)->create([
//            'loyalty_program_id' => $merchant->loyaltyProgram->id,
//            'created_at' => \Carbon\Carbon::parse($faker->dateTimeBetween('-1 year' , '-5 months'))->toDateTimeString()
//            ]);
////        $offers = factory(Offer::class, 5)->create(['loyalty_program_id' => $merchant->loyaltyProgram->id,]);
//        $passes = factory(Pass::class, 50)->create(['user_id' => $merchant->id]);
//        $shops = factory(\App\Models\Shop::class, 10)->create(['user_id' => $merchant->id]);
//        $areaManagers = factory(\App\Models\AreaManager::class, 10)->create(['user_id' => $merchant->id]);
//        $merchant->shops->map(function ($item) use ($merchant) {
//            /** @var \App\Models\Shop $item */
//            $manager = $merchant->areaManagers()->whereDoesntHave('shops')->first();
//            $item->areaManagers()->attach($manager->id);
//        });
//
//        $clients->map(function ($client) use ($merchant, $lp, $offers, $passes, $faker, $shops, $areaManagers) {
//            /** @var \App\Models\AreaManager $manager */
//            $manager = $areaManagers->random();
//            factory(Invite::class)->create([
//                'area_manager_id' => $manager->id,
//                'shop_id' => $manager->shops()->first()->id
//            ]);
//
//            // client make purchase
//            for ($i = 0; $i <= $faker->numberBetween(20, 120); $i++) {
//                $amount = round($faker->numberBetween(75, 2000), -1);
//                $shop_id = $shops->random()->id;
//                factory(\App\Models\ClientShop::class)->create([
//                    'client_id' => $client->id,
//                    'shop_id' => $shop_id,
//                    'amount' => $amount,
//                    'point' => DB::raw('point+' . intdiv($amount, $lp->currency_value)),
//                    'type' => \App\Models\ClientShop::TYPE_LOYALTY
//                ]);
//                Transaction::create([
//                    'client_id' => $client->id,
//                    'amount' => $amount,
//                    'point' => intdiv($amount, $lp->currency_value),
//                    'shop_id' => $shop_id,
//                    'status' => 1,
//                    'currency' => $lp->currency
//                ]);
//                factory(\App\Models\Log::class)->create([
//                    'message' => 'Received ' . intdiv($amount, $lp->currency_value) . ' points to card',
//                    'point' => intdiv($amount, $lp->currency_value),
//                    'shop_id' => $shop_id,
//                    'area_manager_id' => auth()->id(),
//                    'amount' => $amount,
//                    'logable_id' => $client->id,
//                    'logable_type' => 'App\Models\Client'
//                    ]);
//            }
//
//            // client make offer
//            for ($i = 0; $i <= $faker->numberBetween(15, 30); $i++) {
//                $offer = $offers->random();
//                $shop_id = $shops->random()->id;
//                if (!$client->offers()->where('offers.id', $offer->id)->exists()) {
//                    $client_offer = factory(\App\Models\ClientOffer::class)->create([
//                        'shop_id' => $shop_id,
//                        'offer_id' => $offer->id,
//                        'client_id' => $client->id,
//                        'created_by' => $client->id,
//                        'created_at' => \Carbon\Carbon::parse($faker->dateTimeBetween('-1 year' , 'now'))->toDateTimeString()
//                    ]);
//                    Transaction::create([
//                        'client_id' => $client->id,
//                        'point' => $offer->points_cost,
//                        'shop_name' => 'Buy offer',
//                        'status' => 0
//                    ]);
//                    if ($client->clientLoyaltyProgram->point >= $offer->points_cost) {
//
//                        $client->increment('lifetime_value', $offer->points_cost);
//                        factory(\App\Models\Log::class)->create([
//                            'message' => 'Use offer ' . $offer->name,
//                            'point' => '-' . $offer->points_cost,
//                            'logable_id' => $client->id,
//                            'logable_type' => 'App\Models\Client'
//                            ]);
//                    }
//
//                    if ($data = $client->offers()->where('offers.id', $offer->id)->updateExistingPivot($offer->id, ['used' => 1, 'shop_id' => $shop_id])) {
//                        factory(\App\Models\Log::class)->create([
//                            'message' => 'Redeem offer ' . $client->offers()->where('offers.id', $offer->id)->first()->name,
//                            'point' => 0,
//                            'shop_id' => $shop_id,
//                            'area_manager_id' => auth()->id(),
//                            'amount' => round($faker->numberBetween(75, 1000), -1) ?? 0,
//                            'logable_id' => $client->id,
//                            'logable_type' => 'App\Models\Client'
//                        ]);
//                    }
//                }
//            }
//
//            //client use pass
//            for ($i = 0; $i <= 50; $i++) {
//                /** @var \App\Models\Client $client */
//                $pass = $passes->random();
//                $shop = $shops->random();
//                if (!$client->passes()->where('id', $pass->id)->exists()) {
//                    $client->passes()->attach($pass->id, [
//                        'shop_id' => $shop->id,
//                        'created_by' => $client->id,
//                        'created_at' => \Carbon\Carbon::parse($faker->dateTimeBetween('-1 year' , 'now'))->toDateTimeString()
//                    ]);
//                    $client->logs()->create([
//                        'message' => 'Redeem coupon ' . $client->passes()->where('id', $pass->id)->first()->title,
//                        'point' => 0,
//                        'shop_id' => $shop->id,
//                        'area_manager_id' => auth()->id()
//                    ]);
//                }
//            }
//
//        });
//
//        DB::commit();
//        return response()->json([
//            'status' => true
//        ]);
//    });

    Route::post('import-trans', function () {

    });

    Route::get('dummy-trans', function () {
        $clients = Client::where('user_id', 66)->get();
        $clients->map(function ($item) {
            /** @var Client $item */
            $item->clientShops->map(function ($item) {
                /** @var ClientShop $item */
                $item->products()->createMany([
                    [
                        'name' => 'chocolate croissant',
                        'value' => 12,
                        'quantity' => 1
                    ],
                    [
                        'name' => 'banana cakes',
                        'value' => 3,
                        'quantity' => 6
                    ]
                ]);
            });
        });
    });

    Route::get('dummy-trans', function () {
        $user = User::find(66)->clients->map(function ($client) {
            /** @var Client $client */
            $client->transactions->map(function ($trans) {
                $date = Carbon::now()
                    ->subMonths(rand(0, 10))
                    ->subDays(rand(0, 25))
                    ->subMinutes(rand(0, 50))
                    ->toDateTimeString();
                /** @var Transaction $trans */
                $clientShop = $trans->client->clientShops()
                    ->where('shop_id', $trans->shop_id)
                    ->where('amount', $trans->amount)
                    ->where('point', $trans->point)
                    ->first();
                if ($clientShop) {
                    $clientShop->timestamps = false;
                    $clientShop->created_at = $date;
                    $clientShop->updated_at = $date;
                    $clientShop->save();
                    $trans->timestamps = false;
                    $trans->currency = 'MYR';
                    $trans->created_at = $date;
                    $trans->updated_at = $date;
                    $trans->save();
                }
            });
        });
    });

    Route::get('clientupdate', function () {
        User::find(66)->clients->map(function ($client) {
            $regions = [
                "Malaysia", "Thailand", "Singapore"
            ];
            /** @var $client Client */
            $date = Carbon::now()
                ->subYears(rand(15, 60))
                ->subMonths(rand(0, 10))
                ->subDays(rand(0, 30))
                ->toDateString();
            $client->birthday = $date;
            $client->address = $regions[array_rand($regions)];
            $client->save();
        });
    });

    Route::get('pos-data', function () {
        User::find(66)->clients->map(function ($client) {
            $fruits = [
                [
                    'name' => 'Orange',
                    'value' => 2
                ],
                [
                    'name' => 'Pineapple',
                    'value' => 5,
                ],
                [
                    'name' => 'kiwi',
                    'value' => 2,
                ],
                [
                    'name' => 'Pear',
                    'value' => 4,
                ],
                [
                    'name' => 'Mandarin',
                    'value' => 2,
                ],
                [
                    'name' => 'Quince',
                    'value' => 7,
                ]
            ];
            /** @var Client $client */
            $shops = $client->user->shops;
            $array = [0, 0, 0, 1, 3, 4];
            $rand = $array[array_rand($array)];
            // client make purchase
            for ($i = 0; $i <= $rand; $i++) {
                $fruit = $fruits[array_rand($fruits)] + ['quantity' => rand(1, 10)];
                $amount = $fruit['quantity'] * $fruit['value'];
//                $amount = round(rand(75, 2000), -1);
                $shop_id = $shops->random()->id;
                $clientShop = factory(ClientShop::class)->create([
                    'client_id' => $client->id,
                    'shop_id' => $shop_id,
                    'amount' => $amount,
                    'point' => intdiv($amount, $client->user->loyaltyProgram->currency_value),
                    'type' => ClientShop::TYPE_POS
                ]);
                $clientShop->products()->create($fruit);
                $client->clientLoyaltyProgram()->update([
                    'point' => DB::raw('point + ' . ($client->clientLoyaltyProgram->point + intdiv($amount, $client->user->loyaltyProgram->currency_value)))
                ]);
                factory(Transaction::class)->create([
                    'client_id' => $client->id,
                    'amount' => $amount,
                    'point' => intdiv($amount, $client->user->loyaltyProgram->currency_value),
                    'shop_id' => $shop_id,
                    'status' => 1,
                    'currency' => $client->user->loyaltyProgram->currency,
                    'created_at' => $clientShop->created_at
                ]);
                factory(\App\Models\Log::class)->create([
                    'message' => 'Received ' . intdiv($amount, $client->user->loyaltyProgram->currency_value) . ' points to card',
                    'point' => intdiv($amount, $client->user->loyaltyProgram->currency_value),
                    'shop_id' => $shop_id,
                    'area_manager_id' => auth()->id(),
                    'amount' => $amount,
                    'logable_id' => $client->id,
                    'logable_type' => 'App\Models\Client',
                    'created_at' => $clientShop->created_at
                ]);
            }
        });
    });

    Route::post('register', 'Admin\AuthController@register');
    Route::get('verify/{verification_code}', 'Admin\AuthController@verifyUser');
    Route::post('login', 'Admin\AuthController@login');

    Route::post('forgot', 'Admin\PasswordResetController@sendMail');
    Route::post('confirm', 'Admin\PasswordResetController@validateCode');
    Route::post('change', 'Admin\PasswordResetController@resetPassword');

    Route::group(['middleware' => ['auth:api'],], function () {
        Route::get('logout', 'Admin\AuthController@logout');

        Route::resource('user', 'Admin\UserController');
        Route::post('change-password/{user}', 'Admin\UserController@changePassword');

        Route::get('program/user', 'Admin\LoyaltyProgramController@loyaltyProgram');
        Route::get('program/user/{user}', 'Admin\LoyaltyProgramController@loyaltyProgramByUser');
        Route::get('program/{program}/settings', 'Admin\LoyaltyProgramController@settings');
        Route::put('program/{loyaltyProgram}', 'Admin\LoyaltyProgramController@update');

        Route::resource('terms', 'Admin\ContactsTermController')->only(['index', 'update']);

        Route::resource('score', 'Admin\ScoreController')->only(['index', 'show', 'store', 'update', 'destroy']);

        Route::resource('card', 'Admin\CardController')->only(['index', 'update']);

        Route::resource('location', 'Admin\LocationController')->only(['index', 'store', 'update', 'destroy']);

        Route::resource('offer', 'Admin\OfferController')->except(['edit', 'create']);
        Route::put('offer-card/{card}', 'Admin\OfferController@offerCardUpdate');

        Route::get('offer/{offer}/locations', 'Admin\OfferLocationController@offerLocations');
        Route::resource('offer-location', 'Admin\OfferLocationController')->only(['store', 'show', 'update', 'destroy']);

        Route::resource('pass', 'Admin\PassController')->except(['edit', 'create']);

        Route::get('pass/{pass}/pass-template', 'Admin\PassTemplateController@getTemplate');
        Route::resource('pass-template', 'Admin\PassTemplateController')->only(['show', 'update']);

        Route::get('pass/{pass}/pass-location', 'Admin\PassLocationController@getTemplate');
        Route::resource('pass-location', 'Admin\PassLocationController');

        Route::get('merchant-client/{user}', 'Admin\ClientController@merchantClients');
        Route::resource('client', 'Admin\ClientController')->only(['store', 'show', 'update', 'destroy']);

        Route::get('stamps/{stamps}', 'Admin\StampsController@show');
        Route::put('stamps/{stamps}', 'Admin\StampsController@update');

        Route::resource('shop', 'Admin\ShopController');

        Route::resource('area-manager', 'Admin\AreaManagerController');

        Route::get('shop-performance', 'Admin\PerformanceController@shop');

        Route::post('notify', 'Admin\NotificationController@notifyAll');
        Route::post('notify/{client}', 'Admin\ClientController@notify');

        Route::get('client-export', 'Admin\ClientController@clientExport');
        Route::get('loyalty-export', 'Admin\ClientController@loyaltyExport');
        Route::get('coupon-export', 'Admin\ClientController@couponExport');
        Route::get('transaction-export', 'Admin\ClientController@transactionExport');

        Route::resource('invite', 'Admin\InviteController')->only(['index', 'destroy']);

        Route::get('welcome-page', 'Admin\WelcomePageController@welcomePage');

        Route::post('change-points/{client}', 'Admin\ClientController@changePoints');
        Route::post('reduce-points/{client}', 'Admin\ClientController@reducePoints');

        Route::post('block/{client}', 'Admin\ClientController@block');
        Route::post('block-pass/{pass}', 'Admin\PassController@block');
        Route::post('block-offer/{offer}', 'Admin\OfferController@block');

        Route::post('update-logo', 'Admin\UserController@logo');
        Route::get('valid-pass', 'Admin\PassController@valided');

        Route::post('set-offer/{client}', 'Admin\ClientController@setOffer');

        Route::get('client-boxes', 'Admin\ClientController@boxes');
        Route::resource('campaign', 'Admin\CampaignController')->only(['index', 'update', 'destroy', 'show']);
        Route::post('campaign-notify', 'Admin\NotificationController@notifyCampaign');
        Route::get('offer-list', 'Admin\PassController@passList');
        Route::put('set-schedule/{campaign}', 'Admin\CampaignController@setSchedule');

        Route::put('set-url', 'Admin\UserController@setAppUrl');

        Route::get('chart', 'Admin\WelcomePageController@chart');

        Route::get('transaction-list', 'Admin\TransactionController@transactionList');
        Route::get('transaction-chart', 'Admin\TransactionController@transactionChart');
        Route::get('product-per-shop', 'Admin\ProductController@productPerShop');

        Route::get('client-transaction/{client}', 'Admin\ClientController@clientTransaction');
        Route::get('client-list', 'Admin\ClientController@clientList');

        Route::resource('pos-terminal', 'Admin\POSController');

        Route::get('client-group/list', 'Admin\ClientGroupController@list');
        Route::post('client-group', 'Admin\ClientGroupStoreController@store');
        Route::resource('client-group', 'Admin\ClientGroupController')->except(['store']);
        Route::resource('shop-type', 'Admin\ShopTypeController');

        Route::post('client-transaction/{client}', 'Admin\ClientController@storeTransaction');

        Route::get('campaign-analytics', 'Admin\AnalyticsController@data');

        Route::post('business-rules', 'Admin\BusinessRulesController@segmentation');
        Route::post('business-rules/abc', 'Admin\BusinessRulesController@abc');
        Route::post('business-rules/retention', 'Admin\BusinessRulesController@retention');
        Route::post('business-rules/new-returning', 'Admin\BusinessRulesController@newReturning');

        Route::get('shop-list', 'Admin\ShopController@shopsApi');

    });
});

Route::group(['prefix' => 'client'], function () {

    Route::get('devices', function () {
        return response()->json([
            'entity' => Device::all()
        ]);
    });
    Route::get('client/{id}', 'Client\AuthController@checkClient');
    //tests
    Route::post('register', 'Client\AuthController@register');
    Route::post('login', 'Client\AuthController@login');
    Route::post('social-login', 'Client\AuthController@socialLogin');

    Route::post('forgot', 'Client\PasswordResetController@sendMail');
    Route::post('confirm', 'Client\PasswordResetController@validateCode');
    Route::post('change', 'Client\PasswordResetController@resetPassword');

    Route::group(['middleware' => ['auth:client', 'block']], function () {
        Route::get('merchant', 'Client\AuthController@merchant');
        Route::get('logout', 'Client\AuthController@logout');
        Route::post('set-device', 'Client\AuthController@setDevice');
        Route::get('loyalty-program', 'Client\LoyaltyProgramController@loyaltyProgram');
        Route::get('loyalty-program/terms', 'Client\LoyaltyProgramController@termsConditions');
        Route::get('loyalty-program/offers', 'Client\LoyaltyProgramController@offers');
        Route::get('offer/{offer}/locations', 'Client\LoyaltyProgramController@offerLocations');
        Route::get('active-offer', 'Client\LoyaltyProgramController@offerShow');
        Route::get('check-offer', 'Client\LoyaltyProgramController@checkActiveOffer');
        Route::post('offer/{offer}/buy', 'Client\LoyaltyProgramController@buy');

        Route::resource('pass', 'Client\PassController');

        Route::put('password-update', 'Client\ProfileController@updatePassword');
        Route::put('profile', 'Client\ProfileController@update');
        Route::resource('profile', 'Client\ProfileController')->only(['index']);

        Route::get('transaction', 'Client\TransactionController@index');
        Route::get('shops', 'Client\ProfileController@shops');
    });
});

Route::group(['prefix' => 'manager'], function () {
    Route::get('manager/{id}', 'Manager\AuthController@checkManager');

    Route::post('login', 'Manager\AuthController@login');

    Route::post('forgot', 'Manager\PasswordResetController@sendMail');
    Route::post('confirm', 'Manager\PasswordResetController@validateCode');
    Route::post('change', 'Manager\PasswordResetController@resetPassword');

    Route::group(['middleware' => ['auth:manager']], function () {
        Route::get('logout', 'Manager\AuthController@logout');

        Route::get('shops', 'Manager\ManagerController@shops');
        Route::post('scan/{client}', 'Manager\ManagerController@scan');
        Route::post('phone/{clientPhone}', 'Manager\ManagerController@phone');

        Route::resource('invite', 'Manager\InviteController')->only(['index', 'store']);

        Route::put('password-update', 'Manager\ProfileController@updatePassword');
        Route::put('profile', 'Manager\ProfileController@update');
        Route::resource('profile', 'Manager\ProfileController')->only(['index']);
        Route::get('client/{client}', 'Manager\ManagerController@client');
        Route::get('logo', 'Manager\ProfileController@logo');
        Route::get('card-list', 'Manager\ManagerController@cardList');
        Route::get('client-list/{phone}', 'Manager\ManagerController@clientList');
    });
});

Route::group(['prefix' => 'pos'], function () {
    Route::post('login', 'POS\AuthController@login');
    Route::group(['middleware' => ['auth:pos']], function () {
        Route::post('transaction', 'POS\POSController@scan');
        Route::post('phone-transaction', 'POS\POSController@phone');
    });
});
