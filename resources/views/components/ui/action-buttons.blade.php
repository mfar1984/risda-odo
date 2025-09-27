@props([
    'showUrl' => '',
    'editUrl' => '',
    'deleteUrl' => '',
    'deleteConfirmMessage' => 'Adakah anda pasti untuk memadam item ini?',
    'showView' => true,
    'showEdit' => true,
    'showDelete' => true,
    'customActions' => []
])

<div class="flex justify-center space-x-2">
    @if($showView && $showUrl)
        <a href="{{ $showUrl }}" class="text-blue-600 hover:text-blue-900">
            <span class="material-symbols-outlined" style="font-size: 18px;">visibility</span>
        </a>
    @endif

    @if($showEdit && $editUrl)
        <a href="{{ $editUrl }}" class="text-yellow-600 hover:text-yellow-900">
            <span class="material-symbols-outlined" style="font-size: 18px;">edit</span>
        </a>
    @endif

    @foreach($customActions as $action)
        <a href="{{ $action['url'] }}" class="{{ $action['class'] ?? 'text-gray-600 hover:text-gray-900' }}">
            <span class="material-symbols-outlined" style="font-size: 18px;">{{ $action['icon'] }}</span>
        </a>
    @endforeach

    @if($showDelete && $deleteUrl)
        <form action="{{ $deleteUrl }}" method="POST" class="inline" onsubmit="return confirm('{{ $deleteConfirmMessage }}')">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-red-600 hover:text-red-900">
                <span class="material-symbols-outlined" style="font-size: 18px;">delete</span>
            </button>
        </form>
    @endif
</div>
