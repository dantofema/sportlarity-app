<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $author_id
 * @property string $title
 * @property string|null $description
 * @property string|null $content
 * @property int|null $document_id
 * @property-read Document|null $document
 * @property-read User $user
 * @property-read User $author
 */
class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'author_id',
        'title',
        'description',
        'content',
        'document_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}
