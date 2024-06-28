<?php

use Events\AIEncryptionGuardianEvent;
use Events\CryptoHeistOpportunityEvent;
use Events\CyberKeyEvent;
use Events\FirewallBreachEvent;
use Events\QuantumEncryptionChallengeEvent;
use Events\ShadowyContactEvent;
use Events\SystemEvent;

return [
    'System Alert' => SystemEvent::class,
    'Cyber key found' => CyberKeyEvent::class,
    'Quantum Encryption Challenge' => QuantumEncryptionChallengeEvent::class,
    'Shadowy Contact' => ShadowyContactEvent::class,
    'Firewall Breach' => FirewallBreachEvent::class,
    'Crypto Heist Opportunity' => CryptoHeistOpportunityEvent::class,
    'AI Encryption Guardian' => AIEncryptionGuardianEvent::class,
];