<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Card
 *
 * @property-read \App\Models\LoyaltyProgram $loyaltyProgram
 * @method static \Illuminate\Database\Eloquent\Builder|Card newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Card newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Card query()
 * @mixin \Eloquent
 * @property-read \App\Models\Stamps $stamps
 */
	class Card extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Client
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Client newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Client newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Client query()
 * @mixin \Eloquent
 */
	class Client extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ContactsTerm
 *
 * @property-read \App\Models\LoyaltyProgram $loyaltyProgram
 * @method static \Illuminate\Database\Eloquent\Builder|ContactsTerm newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContactsTerm newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContactsTerm query()
 * @mixin \Eloquent
 */
	class ContactsTerm extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Location
 *
 * @property-read \App\Models\LoyaltyProgram $loyaltyProgram
 * @method static \Illuminate\Database\Eloquent\Builder|Location newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Location newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Location query()
 * @mixin \Eloquent
 */
	class Location extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\LoyaltyProgram
 *
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyProgram newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyProgram newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyProgram query()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Card $card
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Stamps[] $stamps
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Score[] $contactsTerm
 * @property-read mixed $contacts_term
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Score[] $score
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Location[] $locations
 */
	class LoyaltyProgram extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PasswordReset
 *
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PasswordReset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PasswordReset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PasswordReset query()
 * @mixin \Eloquent
 */
	class PasswordReset extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Score
 *
 * @property-read \App\Models\LoyaltyProgram $loyaltyProgram
 * @method static \Illuminate\Database\Eloquent\Builder|Score newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Score newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Score query()
 * @mixin \Eloquent
 */
	class Score extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Stamps
 *
 * @property-read \App\Models\LoyaltyProgram $loyaltyProgram
 * @method static \Illuminate\Database\Eloquent\Builder|Stamps newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Stamps newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Stamps query()
 * @mixin \Eloquent
 */
	class Stamps extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @mixin \Eloquent
 * @property-read \App\Models\PasswordReset $passwordReset
 * @property-read \App\Models\LoyaltyProgram $loyaltyProgram
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User withoutTrashed()
 */
	class User extends \Eloquent {}
}

