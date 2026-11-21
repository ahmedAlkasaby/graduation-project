<?php

namespace App\Models;

use App\Builders\CharityCaseBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * A beneficiary's profile ("beneficiary profile"): who they are, not what they
 * receive. Their support plans are the assistances.
 */
class CharityCase extends SiteModelSoftDelete
{
    use HasFactory;

    protected $table = 'charity_cases';

    protected $fillable = ['full_name', 'national_id', 'phone', 'volunteer_id'];

    protected $searchable = ['full_name', 'national_id', 'phone'];

    /** A soft deleted volunteer is still shown on the cases they followed. */
    public function volunteer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'volunteer_id')->withTrashed();
    }

    public function assistances(): HasMany
    {
        return $this->hasMany(CaseAssistance::class, 'case_id');
    }

    public function installments(): HasManyThrough
    {
        return $this->hasManyThrough(AssistanceInstallment::class, CaseAssistance::class, 'case_id', 'case_assistance_id');
    }

    /** How the beneficiary is shown in a select: name, then national ID. */
    public function selectLabel(): string
    {
        return $this->full_name.' — '.$this->national_id;
    }

    public function newEloquentBuilder($query): CharityCaseBuilder
    {
        return new CharityCaseBuilder($query);
    }
}
