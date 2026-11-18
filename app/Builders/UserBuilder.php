<?php

namespace App\Builders;

use App\Enums\UserTypeEnum;
use Illuminate\Database\Eloquent\Builder;

class UserBuilder extends BaseBuilder
{
    public function filter(array $filters): static
    {
        return $this->search($filters['search'] ?? null)
            ->ofType($filters['type'] ?? null)
            ->when(isset($filters['active']), fn (Builder $query) => $query->active((bool) $filters['active']))
            ->trashed($filters['trashed'] ?? null)
            ->createdBetween($filters['date_start'] ?? null, $filters['date_end'] ?? null);
    }

    public function ofType(?string $type): static
    {
        return blank($type) || $type === 'all' ? $this : $this->where('type', $type);
    }

    /** @param  list<UserTypeEnum>  $types */
    public function ofTypes(array $types): static
    {
        return $this->whereIn('type', array_map(fn (UserTypeEnum $type): string => $type->value, $types));
    }

    public function ofRole(string $role): static
    {
        return $this->whereHas('roles', fn (Builder $query) => $query->where('name', $role));
    }
}
