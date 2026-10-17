<?php

declare(strict_types=1);

namespace Rimba\Work\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum ActionType: string implements HasColor, HasIcon, HasLabel
{
    case Submit = 'submit';
    case Verify = 'verify';
    case Proceed = 'proceed';
    case Approve = 'approve';
    case Reject = 'reject';
    case Return = 'return';
    case Withdraw = 'withdraw';
    case Cancel = 'cancel';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Submit => 'Submit',
            self::Verify => 'Verify',
            self::Proceed => 'Proceed',
            self::Approve => 'Approve',
            self::Reject => 'Reject',
            self::Return => 'Return',
            self::Withdraw => 'Withdraw',
            self::Cancel => 'Cancel',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::Submit => 'Provide the required information or work and submit it for processing.',

            self::Verify => 'Confirm that the submitted information or work has been checked and is correct, complete, and meets the required criteria.',

            self::Proceed => 'Confirm receipt, awareness, understanding, or concurrence with the submitted information or work and allow processing to proceed.',

            self::Approve => 'Authorize the submitted information, request, or work to proceed.',

            self::Reject => 'Decide that the submitted information, request, or work is not approved and should not proceed.',

            self::Return => 'Send the request back because additional information, clarification, correction, or further work is required.',

            self::Withdraw => 'Retract the request and stop further processing at the request of the requestor.',

            self::Cancel => 'Stop further processing because the request or workflow should no longer continue.',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Submit => 'primary',

            self::Verify,
            self::Proceed,
            self::Approve => 'success',

            self::Return,
            self::Withdraw => 'warning',

            self::Reject,
            self::Cancel => 'danger',
        };
    }

    public function getIcon(): string|Heroicon|null
    {
        return match ($this) {
            self::Submit => Heroicon::PaperAirplane,
            self::Verify => Heroicon::CheckBadge,
            self::Proceed => Heroicon::ArrowRightCircle,
            self::Approve => Heroicon::CheckCircle,
            self::Reject => Heroicon::XCircle,
            self::Return => Heroicon::ArrowUturnLeft,
            self::Withdraw => Heroicon::ArrowLeftCircle,
            self::Cancel => Heroicon::NoSymbol,
        };
    }
}
