<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminComplaintResource extends JsonResource
{
    /**
     * Transform the resource into an array (Decrypted for authenticated Admin).
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ticket_number' => $this->ticket_number,
            'category' => $this->category,
            // Data sensitif pelapor
            'reporter_name' => $this->reporter_name,
            'reporter_nik' => $this->reporter_nik,
            'reporter_phone' => $this->reporter_phone,
            'complaint_title' => $this->complaint_title,
            'complaint_content' => $this->complaint_content,
            'location' => $this->location,
            'status' => $this->status,
            'admin_response' => $this->admin_response,
            'processed_by' => $this->processor?->name,
            'processed_at' => $this->processed_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
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
