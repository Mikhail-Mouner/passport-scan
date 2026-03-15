@if($data)
    @if(is_array($data))
        <div class="row">
            @foreach($data as $key => $value)
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-header">
                            <strong>{{ ucfirst($key) }}</strong>
                        </div>
                        <div class="card-body">
                            @if(is_array($value))
                                <ul class="list-unstyled">
                                    @foreach($value as $subKey => $subValue)
                                        <li class="mb-2">
                                            <strong>{{ ucfirst($subKey) }}:</strong>
                                            @if(is_array($subValue))
                                                <x-post-data :data="$subValue" />
                                            @else
                                                <span class="text-muted">{{ $subValue }}</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-muted">{{ $value }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p>{{ $data }}</p>
    @endif
@endif
