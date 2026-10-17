<?php

declare(strict_types=1);

namespace Rimba\Work\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum StepType: string implements HasColor, HasIcon, HasLabel
{
    case Submit = 'submit';
    case Verify = 'verify';
    case Acknowledge = 'acknowledge';
    case Approve = 'approve';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Submit => 'Submitting',
            self::Verify => 'Verifying',
            self::Acknowledge => 'Acknowledgement',
            self::Approve => 'Approval',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::Submit => 'The requestor provides the required information or work and submits it for processing. The requestor may withdraw the request to stop further processing.',

            self::Verify => 'The verifier checks the submitted information or work and confirms that it is verified (correct, complete, and meets the required criteria). The verifier may also return the request for additional information or correction, or cancel the request to stop further processing.',

            self::Acknowledge => 'The acknowledger confirms receipt, awareness, understanding, or concurrence with the submitted information or work by agreeing to proceed. The acknowledger may also return the request for additional information or correction, or cancel the request to stop further processing.',

            self::Approve => 'The approver evaluates the submitted information or work and decides whether it is approved or rejected. The approver may also return the request for additional information or correction.',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Submit => 'primary',
            self::Verify => 'warning',
            self::Acknowledge => 'info',
            self::Approve => 'success',
        };
    }

    public function getIcon(): string|Heroicon|null
    {
        return match ($this) {
            self::Submit => Heroicon::DocumentArrowUp,
            self::Verify => Heroicon::ClipboardDocumentCheck,
            self::Acknowledge => Heroicon::HandThumbUp,
            self::Approve => Heroicon::ShieldCheck,
        };
    }

    /**
     * @return array<ActionType>
     */
    public function allowedActionTypes(): array
    {
        return match ($this) {
            self::Submit => [
                ActionType::Submit,
            ],

            self::Verify => [
                ActionType::Verify,
                ActionType::Return,
                ActionType::Cancel,
            ],

            self::Acknowledge => [
                ActionType::Proceed,
                ActionType::Return,
                ActionType::Cancel,
            ],

            self::Approve => [
                ActionType::Approve,
                ActionType::Reject,
                ActionType::Return,
            ],
        };
    }

    public function primaryAction(): ActionType
    {
        return match ($this) {
            self::Submit => ActionType::Submit,
            self::Verify => ActionType::Verify,
            self::Acknowledge => ActionType::Proceed,
            self::Approve => ActionType::Approve,
        };
    }

    public function allows(ActionType $actionType): bool
    {
        return in_array($actionType, $this->allowedActionTypes(), true);
    }
}
