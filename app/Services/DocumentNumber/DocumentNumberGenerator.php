<?php

namespace App\Services\DocumentNumber;

use App\Models\Document;
use App\Models\DocumentNumber;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class DocumentNumberGenerator
{
    /**
     * @var \App\Models\DocumentNumber
     */
    private $documentNumber;

    /**
     * @var array
     */
    private $rememberIgnoredCharsLocation = [];

    /**
     * @var \Illuminate\Support\Carbon
     */
    private Carbon $date;

    /**
     * InvoiceNumberService constructor.
     *
     * @param \App\Models\DocumentNumber $documentNumber
     * @param \Illuminate\Support\Carbon $date
     */
    public function __construct(DocumentNumber $documentNumber, Carbon $date)
    {
        $this->documentNumber = $documentNumber;
        $this->date           = $date;
    }

    /**
     * @return string
     */
    public function getNext(): string
    {
        $counter  = $this->getCounter() + 1;
        $template = $this->documentNumber->format;

        if ($this->date->isToday()) {
            if ($counter === 1) {
                $this->documentNumber->update(['current_counter' => 0]);
            } else {
                $counter += $this->documentNumber->current_counter;
            }
        }

        $template = $this->ignoreCharactersAfterHash($template);
        $template = $this->replaceDateCharacters($template);
        $template = $this->replaceWithCounter($template, $counter);
        $template = $this->restoreIgnoredCharacters($template);

        return $template;
    }

    /**
     * @return int
     */
    public function getCounter(): int
    {
        if ($this->documentNumber->period === DocumentNumber::PERIOD_YEARLY) {
            $fromDate = $this->date->copy()->startOfYear();
            $toDate   = $this->date->copy()->endOfYear();
        } elseif ($this->documentNumber->period === DocumentNumber::PERIOD_MONTHLY) {
            $fromDate = $this->date->copy()->startOfMonth();
            $toDate   = $this->date->copy()->endOfMonth();
        } elseif ($this->documentNumber->period === DocumentNumber::PERIOD_DAILY) {
            $fromDate = $this->date->copy()->startOfDay();
            $toDate   = $this->date->copy()->endOfDay();
        } else {
            $fromDate = null;
            $toDate   = null;
        }

        $document = Document::withoutGlobalScope('company_documents_only')
                            ->where('invoice_number_id', $this->documentNumber->id)
                            ->when($fromDate && $toDate, static function (Builder $query) use ($toDate, $fromDate) {
                                $query->where('date_created', '>=', $fromDate);
                                $query->where('date_created', '<=', $toDate);
                            })
                            ->orderBy('created_at', 'DESC')
                            ->first('number_counter');

        return $document->number_counter ?? 0;
    }

    /**
     * @param $template
     *
     * @return string
     */
    private function ignoreCharactersAfterHash($template): string
    {
        for ($i = 0; $i < strlen($template); $i++) {
            if ($template[$i] === '#') {
                $this->rememberIgnoredCharsLocation[$i] = $template[$i + 1];

                $template = substr_replace($template, 'Ø', $i, 2);
                $i--;
            }
        }

        return $template;
    }

    /**
     * @param string $template
     *
     * @return string
     */
    private function replaceDateCharacters(string $template): string
    {
        $replaceables = [
            'RRRR' => $this->date->format('Y'),
            'RR'   => $this->date->format('y'),
            'MM'   => $this->date->format('m'),
            'DD'   => $this->date->format('d'),
        ];

        foreach ($replaceables as $key => $value) {
            $template = Str::replace($key, $value, $template);
        }

        return $template;
    }

    /**
     * @param string $template
     * @param int    $counter
     *
     * @return string
     */
    private function replaceWithCounter(string $template, int $counter): string
    {
        $cCharCount = Str::substrCount($template, 'C');

        for ($i = $cCharCount; $i > 0; $i--) {
            $template = Str::replace(
                Str::padRight('', $i, 'C'),
                Str::padLeft($counter, $i, '0'),
                $template
            );
        }

        return $template;
    }

    /**
     * @param string $template
     *
     * @return mixed|string
     */
    private function restoreIgnoredCharacters(string $template): string
    {
        $loop = 0;

        foreach ($this->rememberIgnoredCharsLocation as $index => $value) {
            $template = substr_replace($template, $value, $index + $loop, 0);
            $loop++;
        }

        // remove Ø characters
        return str_replace('Ø', '', $template);
    }
}
