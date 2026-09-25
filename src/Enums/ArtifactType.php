<?php

declare(strict_types=1);

namespace Rimba\Work\Enums;

enum ArtifactType: string
{
    case Data = 'data';
    case Record = 'record';
    case Document = 'document';
    case Material = 'material';
    case Decision = 'decision';
    case Notification = 'notification';
}
