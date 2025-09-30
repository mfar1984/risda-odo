@props([
    'status' => '',
    'statusMap' => [
        'aktif' => ['label' => 'Aktif', 'class' => 'bg-green-100 text-green-800'],
        'tidak_aktif' => ['label' => 'Tidak Aktif', 'class' => 'bg-red-100 text-red-800'],
        'gantung' => ['label' => 'Digantung', 'class' => 'bg-yellow-100 text-yellow-800'],
        'digantung' => ['label' => 'Digantung', 'class' => 'bg-yellow-100 text-yellow-800'],
        'penyelenggaraan' => ['label' => 'Penyelenggaraan', 'class' => 'bg-yellow-100 text-yellow-800'],
        'draf' => ['label' => 'Draf', 'class' => 'bg-gray-100 text-gray-800'],
        'lulus' => ['label' => 'Lulus', 'class' => 'bg-blue-100 text-blue-800'],
        'tolak' => ['label' => 'Tolak', 'class' => 'bg-red-100 text-red-800'],
        'selesai' => ['label' => 'Selesai', 'class' => 'bg-green-100 text-green-800'],
        'tertunda' => ['label' => 'Tertunda', 'class' => 'bg-orange-100 text-orange-800'],
        'dijadualkan' => ['label' => 'Dijadualkan', 'class' => 'bg-blue-100 text-blue-800'],
        'dalam_proses' => ['label' => 'Dalam Proses', 'class' => 'bg-yellow-100 text-yellow-800'],
    ]
])

@php
    $statusConfig = $statusMap[$status] ?? ['label' => ucfirst($status), 'class' => 'bg-gray-100 text-gray-800'];
@endphp

<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusConfig['class'] }}" style="font-family: Poppins, sans-serif !important; font-size: 10px !important;">
    {{ $statusConfig['label'] }}
</span>
