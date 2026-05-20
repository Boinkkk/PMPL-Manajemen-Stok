<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditTrailTest extends TestCase
{
    use RefreshDatabase;

    public function test_audit_index_can_be_opened_without_authentication(): void
    {
        $response = $this->get(route('audit.index'));

        $response->assertOk();
        $response->assertSee('Audit Trail');
    }
}
