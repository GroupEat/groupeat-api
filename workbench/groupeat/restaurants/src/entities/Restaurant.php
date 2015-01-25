<?php namespace Groupeat\Restaurants\Entities;

use Carbon\Carbon;
use Config;
use Groupeat\Auth\Entities\Interfaces\User;
use Groupeat\Auth\Entities\Traits\HasCredentials;
use Groupeat\Support\Entities\Entity;
use Groupeat\Support\Exceptions\BadRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Restaurant extends Entity implements User {

    use HasCredentials, SoftDeletingTrait;

    protected $fillable = ['name', 'phoneNumber'];


    public function getRules()
    {
        return [
            'name' => 'required',
            'phoneNumber' => ['regex:/^0[0-9]([ .-]?[0-9]{2}){4}$/'],
        ];
    }

    public function categories()
    {
        return $this->belongsToMany('Groupeat\Restaurants\Entities\Category');
    }

    public function address()
    {
        return $this->hasOne('Groupeat\Restaurants\Entities\Address');
    }

    public function products()
    {
        return $this->hasMany('Groupeat\Restaurants\Entities\Product');
    }

    public function openingWindows()
    {
        return $this->hasMany('Groupeat\Restaurants\Entities\OpeningWindow');
    }

    public function closingWindows()
    {
        return $this->hasMany('Groupeat\Restaurants\Entities\ClosingWindow');
    }

    public function scopeAround(Builder $query, $latitude, $longitude, $distanceInKms = null)
    {
        $distanceInKms = $distanceInKms ?: Config::get('restaurants::around_distance_in_kilometers');

        $query->whereHas('address', function(Builder $subQuery) use ($latitude, $longitude, $distanceInKms)
        {
            $address = $subQuery->getModel();
            $table = $address->getTable();

            $subQuery->whereRaw('(2 * (3959 * ATAN2(
                SQRT(
                    POWER(SIN(RADIANS('.$latitude.' - "'.$table.'"."latitude") / 2), 2) +
                    COS(RADIANS("'.$table.'"."latitude")) *
                    COS(RADIANS('.$latitude.')) *
                    POWER(SIN(RADIANS('.$longitude.' - "'.$table.'"."longitude") / 2), 2)
                ),
                SQRT(1 - (
                        POWER(SIN(RADIANS('.$latitude.' - "'.$table.'"."latitude") / 2), 2) +
                        COS(RADIANS("'.$table.'"."latitude")) *
                        COS(RADIANS('.$latitude.')) *
                        POWER(SIN(RADIANS('.$longitude.' - "'.$table.'"."longitude") / 2), 2)
                    ))
            )) <= '.$distanceInKms.')');
        });
    }

    public function assertOpened(Carbon $from = null, Carbon $to = null)
    {
        $from = $from ?: Carbon::now();
        $to = $to ?: $from->copy()->addMinutes(Config::get('restaurants::opening_duration_in_minutes'));
        $restaurant = static::opened($from, $to)->where($this->getTableField('id'), $this->id)->first();

        if (!$restaurant || $restaurant->id != $this->id)
        {
            throw new BadRequest("The {$this->toShortString()} is not opened from $from to $to.");
        }
    }

    public function scopeOpened(Builder $query, Carbon $from = null, Carbon $to = null)
    {
        $from = $from ?: Carbon::now();
        $to = $to ?: $from->copy()->addMinutes(Config::get('restaurants::opening_duration_in_minutes'));

        $query->whereHas('openingWindows', function(Builder $subQuery) use ($from, $to)
        {
            $openingWindow = $subQuery->getModel();

            $subQuery->where($openingWindow->getTableField('dayOfWeek'), $from->dayOfWeek)
                ->where($openingWindow->getTableField('from'), '<=', $from->toTimeString())
                ->where($openingWindow->getTableField('to'), '>=', $to->toTimeString());
        });

        $query->whereDoesntHave('closingWindows', function(Builder $subQuery) use ($from, $to)
        {
            $closingWindow = $subQuery->getModel();

            $subQuery->where($closingWindow->getTableField('day'), $from->toDateString())
                ->where($closingWindow->getTableField('from'), '<=', $from->toTimeString())
                ->where($closingWindow->getTableField('to'), '>=', $to->toTimeString());
        });
    }

}
