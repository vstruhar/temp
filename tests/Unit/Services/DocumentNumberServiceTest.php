<?php

namespace Tests\Unit\Services;

use App\Models\Document;
use App\Models\DocumentNumber;
use App\Models\Team;
use App\Models\User;
use App\Services\DocumentNumberService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentNumberServiceTest extends TestCase
{
    use RefreshDatabase;

    private $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Team::factory()->create();
        $user          = User::factory()->create(['current_team_id' => $this->company->id]);

        $this->actingAs($user);
    }

    /** @test */
    public function it_should_generate_valid_document_number()
    {
        $documentNumber = $this->getInvoiceNumber();

        $cases = [
            'CRR'         => '1' . now()->format('y'),
            'CCRR'        => '01' . now()->format('y'),
            'CCCRR'       => '001' . now()->format('y'),
            'CCCRRRR'     => '001' . now()->year,
            'CCCMMRRRR'   => '001' . now()->format('mY'),
            'CCCRRRRMM'   => '001' . now()->format('Ym'),
            'CCCRRMM'     => '001' . now()->format('ym'),
            'CCCRRMMDD'   => '001' . now()->format('ymd'),
            'CCCRRRRMMDD' => '001' . now()->format('Ymd'),
        ];

        foreach ($cases as $template => $expectedResult) {
            $documentNumber->format = $template;

            $this->assertSame($expectedResult, DocumentNumberService::getNext($documentNumber, now()));
        }
    }

    /** @test */
    public function it_should_ignore_character_that_has_hash_symbol_before_it()
    {
        $documentNumber = $this->getInvoiceNumber();

        $cases = [
            '#CCRR'              => 'C1' . now()->format('y'),
            'CC#CRR'             => '01C' . now()->format('y'),
            'CCC#RRR'            => '001R' . now()->format('y'),
            '#CCC#RRR'           => 'C01R' . now()->format('y'),
            'CCC#MMMRRRR'        => '001M' . now()->format('mY'),
            'CCCRRMM#DDD'        => '001' . now()->format('ym') . 'D' . now()->format('d'),
            '#CCC#RRRRR#MMM#DDD' => 'C01R' . now()->year . 'M' . now()->format('m') . 'D' . now()->format('d'),
            '#CCCRR#RRRDDDD'     => 'C01' . now()->format('y') . 'R' . now()->format('ydd'),
        ];

        foreach ($cases as $template => $expectedResult) {
            $documentNumber->format = $template;

            $this->assertSame($expectedResult, DocumentNumberService::getNext($documentNumber, now()));
        }
    }

    /** @test */
    public function it_should_be_able_to_handle_other_chracters_without_replacing()
    {
        $documentNumber = $this->getInvoiceNumber();

        $cases = [
            'MY-RRCC'      => 'MY-' . now()->format('y') . '01',
            'SPE#C_CCC_RR' => 'SPEC_001_' . now()->format('y'),
            '$ASD-RR/CCC'  => '$ASD-' . now()->format('y') . '/001',
            'ASD-!RR/CCC'  => 'ASD-!' . now()->format('y') . '/001',
            'RR/CCC_MY'    => now()->format('y') . '/001_MY',
        ];

        foreach ($cases as $template => $expectedResult) {
            $documentNumber->format = $template;

            $this->assertSame($expectedResult, DocumentNumberService::getNext($documentNumber, now()));
        }
    }

    /** @test */
    public function it_should_use_counter_properly()
    {
        $documentNumber = $this->getInvoiceNumber(['period' => DocumentNumber::PERIOD_YEARLY]);

        Document::factory()->create([
            'company_id'        => $this->company->id,
            'invoice_number_id' => $documentNumber->id,
            'number'            => now()->year . '/001',
        ]);

        $this->assertSame(now()->year . '/002', DocumentNumberService::getNext($documentNumber, now()));
    }

    /** @test */
    public function it_should_reset_counter_yearly()
    {
        $documentNumber = $this->getInvoiceNumber(['period' => DocumentNumber::PERIOD_YEARLY]);

        $this->assertSame(now()->year . '/001', DocumentNumberService::getNext($documentNumber, now()));

        $this->travel(1)->days();

        Document::factory()->create([
            'company_id'        => $this->company->id,
            'invoice_number_id' => $documentNumber->id,
            'number'            => now()->year . '/001',
        ]);

        $this->assertSame(now()->year . '/002', DocumentNumberService::getNext($documentNumber->fresh(), now()));

        // new year
        $this->travel(1)->years();

        $this->assertSame(now()->year . '/001', DocumentNumberService::getNext($documentNumber->fresh(), now()));

        Document::factory()->create([
            'company_id'        => $this->company->id,
            'invoice_number_id' => $documentNumber->id,
            'number'            => now()->year . '/001',
        ]);

        $this->assertSame(now()->year . '/002', DocumentNumberService::getNext($documentNumber->fresh(), now()));
    }

    /** @test */
    public function it_should_reset_counter_monthly()
    {
        $documentNumber = $this->getInvoiceNumber(['period' => DocumentNumber::PERIOD_MONTHLY]);

        $this->assertSame(now()->year . '/001', DocumentNumberService::getNext($documentNumber, now()));

        $this->travel(1)->days();

        Document::factory()->create([
            'company_id'        => $this->company->id,
            'invoice_number_id' => $documentNumber->id,
            'number'            => now()->year . '/001',
        ]);

        $this->assertSame(now()->year . '/002', DocumentNumberService::getNext($documentNumber->fresh(), now()));

        // new month
        $this->travel(1)->months();

        $this->assertSame(now()->year . '/001', DocumentNumberService::getNext($documentNumber->fresh(), now()));

        Document::factory()->create([
            'company_id'        => $this->company->id,
            'invoice_number_id' => $documentNumber->id,
            'number'            => now()->year . '/001',
        ]);

        $this->assertSame(now()->year . '/002', DocumentNumberService::getNext($documentNumber->fresh(), now()));
    }

    /** @test */
    public function it_should_reset_counter_daily()
    {
        $documentNumber = $this->getInvoiceNumber(['period' => DocumentNumber::PERIOD_DAILY]);

        $this->assertSame(now()->year . '/001', DocumentNumberService::getNext($documentNumber, now()));

        $this->travel(1)->minutes();

        Document::factory()->create([
            'company_id'        => $this->company->id,
            'invoice_number_id' => $documentNumber->id,
            'number'            => now()->year . '/001',
        ]);

        $this->assertSame(now()->year . '/002', DocumentNumberService::getNext($documentNumber->fresh(), now()));

        // new day
        $this->travel(1)->days();

        $this->assertSame(now()->year . '/001', DocumentNumberService::getNext($documentNumber->fresh(), now()));

        Document::factory()->create([
            'company_id'        => $this->company->id,
            'invoice_number_id' => $documentNumber->id,
            'number'            => now()->year . '/001',
        ]);

        $this->assertSame(now()->year . '/002', DocumentNumberService::getNext($documentNumber->fresh(), now()));
    }

    /** @test */
    public function it_should_never_reset_counter()
    {
        $documentNumber = $this->getInvoiceNumber(['period' => DocumentNumber::PERIOD_NONE]);

        $this->assertSame(now()->year . '/001', DocumentNumberService::getNext($documentNumber, now()));

        // days
        $this->travel(2)->days();

        Document::factory()->create([
            'company_id'        => $this->company->id,
            'invoice_number_id' => $documentNumber->id,
            'number'            => now()->year . '/001',
        ]);

        $this->assertSame(now()->year . '/002', DocumentNumberService::getNext($documentNumber, now()));

        // months
        $this->travel(3)->months();

        Document::factory()->create([
            'company_id'        => $this->company->id,
            'invoice_number_id' => $documentNumber->id,
            'number'            => now()->year . '/002',
        ]);

        $this->assertSame(now()->year . '/003', DocumentNumberService::getNext($documentNumber, now()));

        // years
        $this->travel(3)->years();

        Document::factory()->create([
            'company_id'        => $this->company->id,
            'invoice_number_id' => $documentNumber->id,
            'number'            => now()->year . '/003',
        ]);

        $this->assertSame(now()->year . '/004', DocumentNumberService::getNext($documentNumber, now()));
    }

    /** @test */
    public function it_should_use_current_counter_whn_using_current_period()
    {
        $documentNumber = $this->getInvoiceNumber([
            'period'          => DocumentNumber::PERIOD_YEARLY,
            'current_counter' => 3,
        ]);

        Document::factory()->create([
            'company_id'        => $this->company->id,
            'invoice_number_id' => $documentNumber->id,
            'number'            => now()->year . '/001',
        ]);

        $this->assertSame(now()->year . '/005', DocumentNumberService::getNext($documentNumber, now()));
    }

    /** @test */
    public function it_should_reset_current_counter_when_next_period_starts()
    {
        $documentNumber = $this->getInvoiceNumber([
            'period'          => DocumentNumber::PERIOD_YEARLY,
            'current_counter' => 3,
        ]);

        Document::factory()->create([
            'company_id'        => $this->company->id,
            'invoice_number_id' => $documentNumber->id,
            'number'            => now()->year . '/001',
        ]);

        $this->travel(1)->years();

        $this->assertSame(now()->year . '/001', DocumentNumberService::getNext($documentNumber, now()));
        $this->assertSame(0, $documentNumber->fresh()->current_counter);
    }

    /**
     * @param array $data
     *
     * @return mixed
     */
    private function getInvoiceNumber($data = [])
    {
        return DocumentNumber::create(
            array_merge([
                'company_id' => $this->company->id,
                'name'       => 'Test',
                'type'       => 'invoice',
                'period'     => 'yearly',
                'format'     => 'RRRR/CCC',
                'is_default' => true,
            ], $data)
        );
    }
}
