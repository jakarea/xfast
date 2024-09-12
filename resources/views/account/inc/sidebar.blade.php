@php
    $accountMenu ??= collect();
    $accountMenu = ($accountMenu instanceof \Illuminate\Support\Collection) ? $accountMenu : collect();
@endphp
<aside>
    <div class="inner-box">
        <div class="user-panel-sidebar">
            {{--<label class="switch">
                <input type="checkbox" id="toggleSwitch" @checked(auth()->user()->business == 1)>
                <span class="slider"></span>
            </label>--}}
            @if (auth()->user()) 
           
            <form id="switchForm" method="GET" action="/account/business/switch-profile">
                @csrf
                <p>Switch to
                    <span>
                    @if(auth()->user()->hasRole('business-owner'))
                        normal profile
                    @else
                        business profile
                    @endif
                </span>
                </p>
                <label class="switch">
                    <input type="checkbox" id="toggleSwitch" name="business" value="1" @checked(auth()->user()->hasRole('business-owner'))>
                    <span class="slider"></span>
                </label>
                <input type="hidden" name="business" id="businessInput" value="{{ auth()->user()->business }}">
            </form>
            @endif
            @if ($accountMenu->isNotEmpty())
                @foreach($accountMenu as $group => $menu)
                    @php
                        $boxId = str($group)->slug();
                    @endphp
                    <div class="collapse-box">
                        <h5 class="collapse-title no-border">
                            {{ $group }}&nbsp;
                            <a href="#{{ $boxId }}" data-bs-toggle="collapse" class="float-end"><i
                                        class="fa-solid fa-angle-down"></i></a>
                        </h5>
                        @foreach($menu as $key => $value)
                            <div class="panel-collapse collapse show" id="{{ $boxId }}">
                                <ul class="acc-list">
                                    <li>
                                        <a {!! $value['isActive'] ? 'class="active"' : '' !!} href="{{ $value['url'] }}">
                                            <i class="{{ $value['icon'] }}"></i> {{ $value['name'] }}
                                            @if (!empty($value['countVar']))
                                                <span class="badge badge-pill{{ $value['cssClass'] ?? '' }}">
													{{ \App\Helpers\Num::short($value['countVar']) }}
												</span>
                                            @endif
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            @endif

        </div>
    </div>
</aside>

<style>
    /* Switch button styles */
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 34px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        border-radius: 50%;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
    }

    input:checked + .slider {
        background-color: #2196F3;
    }

    input:checked + .slider:before {
        transform: translateX(26px);
    }
</style>

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#toggleSwitch').change(function () {
                // Check if the switch is on or off
                var isChecked = $(this).is(':checked');

                // Set the value of the hidden input to 1 or 0 based on the checkbox state
                $('#businessInput').val(isChecked ? 1 : 0);

                // Submit the form
                $('#switchForm').submit();
            });
        });
    </script>
@endpush
