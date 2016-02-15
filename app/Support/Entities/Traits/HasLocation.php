<?php
namespace Groupeat\Support\Entities\Traits;

use Groupeat\Support\Entities\Abstracts\Entity;
use Groupeat\Support\Exceptions\Exception;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Phaza\LaravelPostgis\Eloquent\Builder as PostgisBuilder;
use Phaza\LaravelPostgis\Geometries\Geometry;
use Phaza\LaravelPostgis\Geometries\Point;
use Treffynnon\Navigator;

trait HasLocation
{
    protected $locationPoint;

    public function distanceInKmsWith(Entity $other): float
    {
        return $this->distanceInKmsWithPoint($other->location);
    }

    public function distanceInKmsWithPoint(Point $location): float
    {
        $thisLocation = $this->toGeography($this->location);
        $otherLocation = $this->toGeography($location);

        $res = $this->getConnection()->select('SELECT ST_DISTANCE('.$thisLocation.', '.$otherLocation.')');

        return $res[0]->st_distance / 1000;
    }

    public function scopeWithinKilometers(EloquentBuilder $query, Point $location, float $kilometers)
    {
        if (!is_numeric($kilometers)) {
            throw new Exception(
                'invalidDistance',
                "The kilometers must be a numeric value."
            );
        }

        $meters = $kilometers * 1000;

        $thisLocation = $this->getRawTableField('location');
        $otherLocation = $this->toGeography($location);

        $query->whereRaw('ST_DWithin('.$thisLocation.', '.$otherLocation.', '.$meters.', false)');
    }

    public function setRawAttributes(array $attributes, $sync = false)
    {
        $location = &$attributes['location'];
        $location = Geometry::fromWKB($location);
        $this->locationPoint = $location;

        parent::setRawAttributes($attributes, $sync);
    }

    public function getPostgisFields()
    {
        return ['location' => Point::class];
    }

    public function newEloquentBuilder($query)
    {
        return new PostgisBuilder($query);
    }

    protected function getLocationAttribute(): Point
    {
        return $this->locationPoint;
    }

    protected function setLocationAttribute(Point $location)
    {
        $this->locationPoint = $location;
        $this->attributes['location'] = $this->getConnection()->raw($this->toGeography($location));
    }

    protected function toGeography(Point $location): string
    {
        return "ST_GeogFromText('".$location->toWKT()."')";
    }
}
