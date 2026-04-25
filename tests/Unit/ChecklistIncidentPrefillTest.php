<?php

use App\Application\Checklists\Data\ChecklistIncidentPrefill;
use Illuminate\Http\Request;

test('checklist incident prefill can be restored from a valid request', function () {
    $prefill = ChecklistIncidentPrefill::fromRequest(
        Request::create('/incidents/new', 'GET', [
            'from' => 'checklist',
            'title' => 'รายงานติดตามจากรายการตรวจเช็ก',
            'category' => 'อื่น ๆ',
            'severity' => 'Medium',
            'description' => "ติดตามต่อจากรายการตรวจเช็กประจำวัน\nรายการที่ไม่เรียบร้อย: Printer",
        ]),
        ['อื่น ๆ', 'ความปลอดภัย'],
        ['Low', 'Medium', 'High'],
    );

    expect($prefill)->not->toBeNull()
        ->and($prefill?->title)->toBe('รายงานติดตามจากรายการตรวจเช็ก')
        ->and($prefill?->category)->toBe('อื่น ๆ')
        ->and($prefill?->severity)->toBe('Medium')
        ->and($prefill?->description)->toContain('Printer');
});

test('checklist incident prefill ignores invalid category and severity values', function () {
    $prefill = ChecklistIncidentPrefill::fromRequest(
        Request::create('/incidents/new', 'GET', [
            'from' => 'checklist',
            'title' => 'รายงานติดตามจากรายการตรวจเช็ก',
            'category' => 'Unknown',
            'severity' => 'Critical',
            'description' => 'ติดตามต่อจากรายการตรวจเช็กประจำวัน',
        ]),
        ['อื่น ๆ', 'ความปลอดภัย'],
        ['Low', 'Medium', 'High'],
    );

    expect($prefill)->not->toBeNull()
        ->and($prefill?->category)->toBeNull()
        ->and($prefill?->severity)->toBeNull();
});

test('checklist incident prefill is absent when the request is not from checklist flow', function () {
    $prefill = ChecklistIncidentPrefill::fromRequest(
        Request::create('/incidents/new', 'GET', [
            'title' => 'General incident',
        ]),
        ['อื่น ๆ'],
        ['Low', 'Medium', 'High'],
    );

    expect($prefill)->toBeNull();
});
