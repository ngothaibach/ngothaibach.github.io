<div class="navbar-top">

    <div class="navbar-top-left">
    <span style="font-size:30px;cursor:pointer;float:left;" onclick="openNav()">&#9776;MENU</span>
        <div class="brand-logo">
            <a href="{{ route('admin.dashboard.index') }}">
                @if (core()->getConfigData('general.design.admin_logo.logo_image', core()->getCurrentChannelCode()))
                    <img src="{{ \Illuminate\Support\Facades\Storage::url(core()->getConfigData('general.design.admin_logo.logo_image', core()->getCurrentChannelCode())) }}" alt="{{ config('app.name') }}" style="height: 40px; width: 110px;"/>
                @else
                    <img src="{{ asset('vendor/webkul/ui/assets/images/logo.png') }}" alt="{{ config('app.name') }}"/>
                @endif
            </a>
        </div>
    </div>

    <div class="navbar-top-right">
        <div class="profile">
            <span class="avatar">
            </span>

            <?php $locale = request()->get('admin_locale') ?: app()->getLocale();?>

            <!-- <div class="profile-info">
                <div class="dropdown-toggle">
                    <div style="display: inline-block; vertical-align: middle;">
                        <span class="name">
                            {{ __('admin::app.datagrid.locale') }}
                        </span>

                        @foreach (core()->getAllLocales() as $localeModel)
                            @if ($localeModel->code == $locale)
                                <span class="role">
                                    {{ $localeModel->name }}
                                </span>
                            @endif
                        @endforeach

                    </div>
                    <i class="icon arrow-down-icon active"></i>
                </div>

                <div class="dropdown-list bottom-right">
                    <div class="control-group">
                        <select class="control" onChange="window.location.href = this.value" style="margin-left: 30px;">
                            @foreach (core()->getAllLocales() as $localeModel)

                                <option value="{{ '?admin_locale=' . $localeModel->code }}" {{ ($localeModel->code) == $locale ? 'selected' : '' }}>
                                    {{ $localeModel->name }}
                                </option>

                            @endforeach
                        </select>
                    </div>
                </div>
            </div> -->
            
            <div class="profile-info">
                <div class="dropdown-toggle">
                    <div style="display: inline-block; vertical-align: middle;">
                        <span class="name">
                            Kho
                        </span>
                        @if(auth()->guard('admin')->user()->role['id'] == 1)
                            @if(Session::get('inventory') == 0)
                                    <span class="role">
                                        Admin
                                    </span>
                            @else
                                @foreach (core()->getInventorySources() as $inventory)
                                    @if($inventory->id == Session::get('inventory'))
                                        <span class="role">
                                            {{ $inventory->name }}
                                        </span>
                                    @endif
                                @endforeach
                            @endif    
                        @else
                            <span class="role">
                                {{core()->getInventorySources()->name}}
                            </span>
                        @endif

                    </div>
                    <i class="icon arrow-down-icon active"></i>
                </div>
                @if(auth()->guard('admin')->user()->role['id'] == 1)
                <div class="dropdown-list bottom-right">
                    <div class="control-group">
                        <select class="control" onChange="onChangeInventory(this.value)" style="margin-left: 30px;">  
                                <option disabled selected >
                                    Chọn kho
                                </option> 
                                <option value= "0" >
                                    Admin
                                </option> 
                            @foreach (core()->getInventorySources() as $inventory)
                                <option value="{{$inventory->id }}">
                                    {{ $inventory->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif
            </div>
            <div class="profile-info">
                <div class="dropdown-toggle">
                    <div style="display: inline-block; vertical-align: middle;">
                        <span class="name">
                            {{ auth()->guard('admin')->user()->name }}
                        </span>

                        <span class="role">
                            {{ auth()->guard('admin')->user()->role['name'] }}
                        </span>
                    </div>
                    <i class="icon arrow-down-icon active"></i>
                </div>

                <div class="dropdown-list bottom-right">
                    <span class="app-version">{{ __('admin::app.layouts.app-version', ['version' => 'v' . config('app.version')]) }}</span>

                    <div class="dropdown-container">
                        <label>Account</label>
                        <ul>
                            <li>
                                <a href="{{ route('shop.home.index') }}" target="_blank">{{ __('admin::app.layouts.visit-shop') }}</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.account.edit') }}">{{ __('admin::app.layouts.my-account') }}</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.session.destroy') }}">{{ __('admin::app.layouts.logout') }}</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>