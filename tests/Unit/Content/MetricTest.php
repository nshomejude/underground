<?php

declare(strict_types=1);

namespace Tests\Unit\Content;

use Domain\Content\Entities\Metric;
use Domain\Shared\ValueObjects\Slug;
use Tests\TestCase;

final class MetricTest extends TestCase
{
    public function test_label_lines_splits_on_the_pipe_and_trims_each_line(): void
    {
        $metric = new Metric(
            slug: Slug::fromString('government-relationships'),
            value: '250+',
            label: ' government relationships |  worldwide ',
            icon: 'globe',
            position: 1,
        );

        $this->assertSame(['government relationships', 'worldwide'], $metric->labelLines());
    }

    public function test_label_lines_drops_empty_segments(): void
    {
        $metric = new Metric(
            slug: Slug::fromString('projects-supported'),
            value: '$20B+',
            label: 'in projects & investments supported||',
            icon: 'coins',
            position: 2,
        );

        $this->assertSame(['in projects & investments supported'], $metric->labelLines());
    }

    public function test_to_array_joins_the_label_lines_with_a_space(): void
    {
        $metric = new Metric(
            slug: Slug::fromString('government-relationships'),
            value: '250+',
            label: 'government relationships | worldwide',
            icon: 'globe',
            position: 1,
        );

        $data = $metric->toArray();

        $this->assertSame('government relationships worldwide', $data['label']);
        $this->assertSame(['government relationships', 'worldwide'], $data['label_lines']);
        $this->assertSame('250+', $data['value']);
    }
}
