<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Repositories;

use Domain\Content\Entities\Project;
use Domain\Content\Repositories\ProjectRepository;
use Domain\Shared\ValueObjects\Slug;
use Infrastructure\Persistence\Eloquent\Models\ProjectRecord;

final class EloquentProjectRepository implements ProjectRepository
{
    public function all(): array
    {
        return ProjectRecord::query()
            ->orderBy('position')
            ->get()
            ->map($this->toEntity(...))
            ->all();
    }

    public function findBySlug(Slug $slug): ?Project
    {
        $record = ProjectRecord::query()->where('slug', $slug->value)->first();

        return $record === null ? null : $this->toEntity($record);
    }

    public function save(Project $project, ?Slug $originalSlug = null): void
    {
        ProjectRecord::query()->updateOrCreate(
            ['slug' => ($originalSlug ?? $project->slug)->value],
            [
                'slug' => $project->slug->value,
                'icon' => $project->icon,
                'title' => $project->title,
                'sector' => $project->sector,
                'body' => $project->body,
                'position' => $project->position,
            ],
        );
    }

    public function delete(Slug $slug): void
    {
        ProjectRecord::query()->where('slug', $slug->value)->delete();
    }

    private function toEntity(ProjectRecord $record): Project
    {
        return new Project(
            slug: Slug::fromString($record->slug),
            icon: $record->icon,
            title: $record->title,
            sector: $record->sector,
            body: $record->body,
            position: $record->position,
        );
    }
}
