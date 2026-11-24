<template>
  <span
    :class="[
      'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold capitalize tracking-wide',
      badgeClass
    ]"
  >
    <span :class="['w-1.5 h-1.5 rounded-full', dotClass]"></span>
    {{ label }}
  </span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  status: {
    type: String,
    required: true,
  },
});

const label = computed(() => {
  const s = props.status?.toLowerCase();
  switch (s) {
    case 'pending':
    case 'menunggu':
      return 'Menunggu';
    case 'diproses':
      return 'Sedang Diproses';
    case 'ditindaklanjuti':
      return 'Ditindaklanjuti';
    case 'selesai':
      return 'Selesai';
    case 'ditolak':
      return 'Ditolak';
    default:
      return props.status || '-';
  }
});

const badgeClass = computed(() => {
  const s = props.status?.toLowerCase();
  switch (s) {
    case 'pending':
    case 'menunggu':
      return 'bg-amber-50 text-amber-700 border border-amber-200';
    case 'diproses':
    case 'ditindaklanjuti':
      return 'bg-blue-50 text-blue-700 border border-blue-200';
    case 'selesai':
      return 'bg-emerald-50 text-emerald-700 border border-emerald-200';
    case 'ditolak':
      return 'bg-rose-50 text-rose-700 border border-rose-200';
    default:
      return 'bg-slate-100 text-slate-700 border border-slate-200';
  }
});

const dotClass = computed(() => {
  const s = props.status?.toLowerCase();
  switch (s) {
    case 'pending':
    case 'menunggu':
      return 'bg-amber-500 animate-pulse';
    case 'diproses':
    case 'ditindaklanjuti':
      return 'bg-blue-500';
    case 'selesai':
      return 'bg-emerald-500';
    case 'ditolak':
      return 'bg-rose-500';
    default:
      return 'bg-slate-400';
  }
});
</script>

