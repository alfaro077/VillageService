<?php

namespace App\Http\Resources\Admin;

use App\Services\OfficialLetterService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminServiceRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array (Decrypted specifically for authenticated Admin).
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $letterService = app(OfficialLetterService::class);
        $whatsappData = $letterService->buildWhatsAppNotification($this->resource);

        return [
            'id' => $this->id,
            'ticket_number' => $this->ticket_number,
            'service_type' => [
                'id' => $this->serviceType?->id,
                'code' => $this->serviceType?->code,
                'name' => $this->serviceType?->name,
            ],
            // Data sensitif warga didekripsi khusus untuk sesi admin yang terautentikasi
            'nik' => $this->nik,
            'kk_number' => $this->kk_number,
            'full_name' => $this->full_name,
            'phone_number' => $this->phone_number,
            'address' => $this->address,
            'purpose_description' => $this->purpose_description,
            'status' => $this->status,
            'letter_number' => $this->letter_number,
            'letter_generated_at' => $this->letter_generated_at?->toIso8601String(),
            'admin_notes' => $this->admin_notes,
            'processed_by' => $this->processor?->name,
            'processed_at' => $this->processed_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'whatsapp' => $whatsappData,
            'documents' => $this->documents->map(fn ($doc) => [
                'id' => $doc->id,
                'document_type' => $doc->document_type,
                'original_name' => $doc->original_name,
                'file_mime' => $doc->file_mime,
                'file_size' => $doc->file_size,
                'download_url' => url("/api/v1/admin/documents/{$doc->id}/download"),
                'view_url' => url("/api/v1/admin/documents/{$doc->id}/view"),
            ]),
        ];
    }
}
