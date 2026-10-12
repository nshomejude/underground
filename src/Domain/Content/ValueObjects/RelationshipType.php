<?php

declare(strict_types=1);

namespace Domain\Content\ValueObjects;

/**
 * The legal/working shape of Underground's relationship to a given
 * location or alliance. A partner is always one of these — never an
 * employee or a subsidiary — and Independent is reserved for locations
 * Underground itself directly operates.
 */
enum RelationshipType: string
{
    case JointVenture = 'joint_venture';
    case Partnership = 'partnership';
    case CooperationAgreement = 'cooperation_agreement';
    case Consultant = 'consultant';
    case Independent = 'independent';

    public function label(): string
    {
        return match ($this) {
            self::JointVenture => 'Joint Venture',
            self::Partnership => 'Partnership',
            self::CooperationAgreement => 'Cooperation Agreement',
            self::Consultant => 'Consultant',
            self::Independent => 'Independent',
        };
    }

    /**
     * A short, honest gloss of what this relationship does — and does
     * not — claim about who the partner is to Underground.
     */
    public function description(): string
    {
        return match ($this) {
            self::JointVenture => 'A dedicated venture formed jointly with an independent local partner for this market — not an Underground subsidiary.',
            self::Partnership => 'An ongoing, named partnership with an independent local firm or principal — not Underground staff.',
            self::CooperationAgreement => 'A defined agreement to cooperate on specific matters, short of a standing partnership — not an employment relationship.',
            self::Consultant => 'An individual or firm retained as an independent consultant, not an employee of Underground.',
            self::Independent => 'Directly operated by Underground; not a third-party relationship.',
        };
    }
}
