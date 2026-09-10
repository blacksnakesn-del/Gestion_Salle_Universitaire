<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Validation\ReservationValidator;
use App\Validation\SalleValidator;
use PHPUnit\Framework\TestCase;

final class ValidationTest extends TestCase
{
    public function testReservationRejectsInvalidEmailAndShortMotif(): void
    {
        $result = (new ReservationValidator())->validate([
            'salle_id' => '1',
            'responsable' => 'Awa Ndiaye',
            'email' => 'invalid',
            'motif' => 'TP',
            'date_debut' => '2030-01-01 10:00',
            'date_fin' => '2030-01-01 12:00',
        ]);

        self::assertFalse($result->isValid());
        self::assertArrayHasKey('email', $result->errors());
        self::assertArrayHasKey('motif', $result->errors());
    }

    public function testSalleRejectsUnknownTypeAndInvalidCapacity(): void
    {
        $result = (new SalleValidator())->validate([
            'nom' => 'Salle A',
            'batiment' => 'Bâtiment A',
            'capacite' => '-1',
            'type' => 'inconnu',
            'active' => '1',
        ]);

        self::assertFalse($result->isValid());
        self::assertArrayHasKey('capacite', $result->errors());
        self::assertArrayHasKey('type', $result->errors());
    }

    public function testValidReservationReturnsTypedAcceptedData(): void
    {
        $result = (new ReservationValidator())->validate([
            'salle_id' => '1',
            'responsable' => 'Awa Ndiaye',
            'email' => 'awa.ndiaye@universite.sn',
            'motif' => 'Cours architecture',
            'date_debut' => '2030-01-01 10:00',
            'date_fin' => '2030-01-01 12:00',
        ]);

        self::assertTrue($result->isValid());
        self::assertIsInt($result->getAcceptedData()['salle_id']);
        self::assertInstanceOf(\DateTimeImmutable::class, $result->getAcceptedData()['date_debut']);
    }
}