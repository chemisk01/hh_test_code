<?php

namespace Cms\Models;

use Cms\Casts\News\NewsMetaCasts;
use Cms\Models\Partners\Partner;
use Cms\ValueObjects\News\NewsMeta;
use EduShare\Domain\ValueObjects\CreatedAt as CreatedAtObject;
use EduShare\Domain\ValueObjects\UpdatedAt as UpdatedAtObject;
use EduShare\Infrastructure\Db\Casts\CreatedAt as CreatedAtCast;
use EduShare\Infrastructure\Db\Casts\UpdatedAt as UpdatedAtCast;
use EduShare\Infrastructure\Db\DomainModelManagerInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Eloquent модель которая используется только для взаимодействия с БД
 *
 * @property int $news_id - идентификатор
 * @property string $title - заголовок
 * @property string $description_short - анонс
 * @property string $description - описание
 * @property string $author - описание
 * @property string $iri - описание
 * @property string $is_active - описание
 * @property string $status - статус новости
 * @property NewsMeta $meta - мета данные
 * @property CreatedAtObject $created_at
 * @property UpdatedAtObject $updated_at
 * @property Collection $partners
 */
class News extends Model
{
    use HasFactory;

    public const STATUS_NEW = 1; // Новая новость
    public const STATUS_APPROVED = 2; // Одобренная новость
    public const STATUS_REJECTED = 3; // Отклоненная новость

    protected $table = 'news';

    protected $primaryKey = 'news_id';

    protected $casts = [
        'meta' => NewsMetaCasts::class,
        'is_active' => 'boolean',
        'created_at' => CreatedAtCast::class,
        'updated_at' => UpdatedAtCast::class
    ];

    protected $fillable = [
        'title',
        'author',
        'description_short',
        'description',
        'iri',
        'meta',
        'is_active',
        'status',
        'created_at',
    ];

    /**
     * @return int[]
     */
    public static function getNewsStatuses(): array
    {
        return [
            self::STATUS_NEW,
            self::STATUS_APPROVED,
            self::STATUS_REJECTED,
        ];
    }

    public function partners(): BelongsToMany
    {
        return $this->belongsToMany(
            Partner::class,
            'news_partner',
            'news_id',
            'partner_id',
            'news_id',
            'partner_id',
        );
    }

    public function tags(): HasMany
    {
        return $this->hasMany(
            Tag::class,
            'entity_id',
            'news_id',
        );
    }
}
