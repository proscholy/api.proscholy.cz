<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
// use ScoutElastic\Searchable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use App\Services\AuthorService;

use App\Elastic\AuthorSearchableTrait;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Venturecraft\Revisionable\RevisionableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Author
 *
 * @property int                                                            $id
 * @property string|null                                                    $name
 * @property string|null                                                    $url
 * @property string|null                                                    $ytchannel
 * @property string|null                                                    $description
 * @property string|null                                                    $email
 * @property string|null                                                    $password
 * @property int|null                                                       $type
 * @property \Carbon\Carbon|null                                            $created_at
 * @property \Carbon\Carbon|null                                            $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Author[]    $isMemberOf
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Author[]    $members
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\SongLyric[] $songLyrics
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\External[]  $externals
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Author whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Author whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Author whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Author whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Author whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Author wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Author whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Author whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Author whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Author whereYtchannel($value)
 * @mixin \Eloquent
 * @property int|null                                                       $visits
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Author whereVisits($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Author[]    $memberships
 */
class Author extends Model
{
    use AuthorSearchableTrait, RevisionableTrait, HasFactory;
    protected $revisionCreationsEnabled = true;
    protected $dontKeepRevisionOf = ['visits'];

    protected $fillable = ['name', 'description', 'email', 'type'];

    protected $authorService;

    private $type_string_values
    = [
        0 => 'autor',
        1 => 'hudební uskupení',
        2 => 'schola',
        3 => 'kapela',
        4 => 'sbor',
    ];

    public function __construct($attributes = array())
    {
        parent::__construct($attributes);
        $this->authorService = app()->make(AuthorService::class);
    }

    public function scopeRestricted($query)
    {
        if (Auth::user()->hasRole('autor')) {
            return $query->whereIn('id', Auth::user()->getAssignedAuthorIds());
        } else {
            return $query;
        }
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(
            Author::class,
            'author_membership',
            'is_member_of',
            'author_id'
        );
    }

    public function memberships(): BelongsToMany
    {
        return $this->belongsToMany(
            Author::class,
            'author_membership',
            'author_id',
            'is_member_of'
        );
    }

    public function song_lyrics(): BelongsToMany
    {
        return $this->belongsToMany(SongLyric::class);
    }

    public function externals(): BelongsToMany
    {
        return $this->belongsToMany(External::class);
    }

    public function files(): BelongsToMany
    {
        return $this->belongsToMany(File::class);
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function getTypeStringAttribute()
    {
        return $this->type_string_values[$this->type];
    }

    public function getTypeStringValuesAttribute()
    {
        return $this->type_string_values;
    }

    public function getSongsOriginalsAttribute()
    {
        return $this->authorService->song_lyrics_include_associated_authors($this)->originals()->orderBy('name')->get();
    }

    public function getSongsTranslationsAttribute()
    {
        return $this->authorService->song_lyrics_include_associated_authors($this)->translations()->orderBy('name')->get();
    }

    public function getSongsInterpretedAttribute()
    {
        return $this->authorService->song_lyrics_interpreted($this)->orderBy('name')->get()
            ->diff($this->songs_originals->merge($this->songs_translations));
    }


    // todo: make obsolete
    public static function getByIdOrCreateWithName($identificator, $uniqueName = false)
    {
        if (is_numeric($identificator)) {
            return Author::find($identificator);
        } else {
            $double = Author::where('name', $identificator)->first();
            if ($uniqueName && $double != null) {
                return $double;
            }

            $author = Author::create([
                'name' => $identificator,
            ]);

            return $author;
        }
    }

    public function getPublicUrlAttribute()
    {
        return url($this->public_route);
    }

    public function getPublicRouteAttribute()
    {
        return "/autor/$this->id";
    }
}
