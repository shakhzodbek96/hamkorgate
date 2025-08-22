<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <div id="sidebar-menu">
            <ul class="metismenu list-unstyled" id="side-menu">
                @if(\App\Services\Helpers\Check::isAdmin())
                    <li>
                        <a href="{{ route('horizon.index') }}" class="waves-effect">
                            <i class="mdi mdi-laravel text-success"></i>
                            <span>Horizon</span>
                        </a>
                    </li>
                @endif
                <li class="menu-title font-size-10"><i>Управление</i></li>
                <li>
                    <a href="{{ route('home') }}" class="waves-effect">
                        <i class="mdi mdi-view-dashboard"></i>
                        <span>Основной</span>
                    </a>
                </li>
                @canany(["Просмотр пользователей","Просмотр разрешений  ","Посмотреть роли"])
                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="fas fa-users-cog"></i>
                            <span key="t-tasks">Контроль доступа</span>
                        </a>
                        <ul class="sub-menu mm-collapse" aria-expanded="false">
                            @can("Просмотр пользователей")
                                <li class="{{ Request::is('users*') ? "mm-active":''}}"><a
                                        href="{{ route('users.index') }}" key="t-task-list">
                                        <i class="fas fa-angle-right px-2"></i>
                                        Пользователи
                                    </a>
                                </li>
                            @endcan
                            @can('Посмотреть роли')
                                <li class="{{ Request::is('roles*') ? "mm-active":''}}"><a
                                        href="{{ route('roles.index') }}" key="t-kanban-board">
                                        <i class="fas fa-angle-right px-2"></i>
                                        Роли
                                    </a>
                                </li>
                            @endcan
                            @can('Просмотр разрешений')
                                <li class="{{ Request::is('permissions*') ? "mm-active":''}}"><a
                                        href="{{ route('permissions.index') }}" key="t-create-task">
                                        <i class="fas fa-angle-right px-2"></i>
                                        Разрешения
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany
                @if(auth()->user()->can('Просмотр партнера') && auth()->user()->is_admin)
                    <li>
                        <a href="{{ route('partners.index') }}" class="waves-effect {{ Request::is('partners*') ? "mm-active":'' }}">
                            <i class="fas fa-briefcase"></i>
                            <span>Партнеры</span>
                        </a>
                    </li>
                @endif
                @can('Просмотр терминалов')
                    <li>
                        <a href="{{ route('terminals.index') }}" class="waves-effect {{ Request::is('terminals*') ? "mm-active":'' }}">
                            <i class="fas fa-calculator"></i>
                            <span>Терминалы</span>
                        </a>
                    </li>
                @endcan
                @can('Просмотр мерчантов')
                    <li>
                        <a href="{{ route('merchants.index') }}" class="waves-effect {{ Request::is('merchants*') ? "mm-active":'' }}">
                            <i class="fas fa-building"></i>
                            <span>Мерчанты</span>
                        </a>
                    </li>
                @endcan
            </ul>
        </div>
    </div>
</div>
