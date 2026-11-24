<?php

namespace App\Http\Resources\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicTrackingResource extends JsonResource
{
    /**
     * Transform the resource into an array (Strictly NO sensitive citizen data).
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $isService = isset($this->service_type_id);

        return [
            'ticket_number' => $this->ticket_number,
            'type' => $isService ? 'service' : 'complaint',
            'title' => $isService ? ($this->serviceType->name ?? 'Layanan Surat') : ($this->category ?? 'Pengaduan Warga'),
            'status' => $this->status,
            'notes' => $isService ? $this->admin_notes : $this->admin_response,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
