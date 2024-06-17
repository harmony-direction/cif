<div class="sidebar">
    <nav class="mt-2">
        <ul class="nav-slide text-center gap-3" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item">
                <a href="{{route('setting')}}" class="nav-link {{ request()->is('setting') ? 'current-page' : '' }}">
                    <span class="material-symbols-outlined" >
                        dashboard
                    </span>
                    <p>
                        แดชบอร์ด
                    </p>
                </a>
            </li>
            <li class="nav-item" id="main-sub-menu">
                <a href="#" class="nav-link {{ request()->is('setting/organization*') ? 'current-page' : '' }}" id="open-sub-menu">
                    <span class="material-symbols-outlined" >
                        location_city
                    </span>
                    <p>
                        องค์กร
                    </p>
                    <span class="material-symbols-outlined icon-right">
                        keyboard_arrow_right
                    </span>
                </a>
                <ul class="nav nav-treeview" id="sub-menu">
                    <li class="nav-item">
                        <a href="{{route('setting.organization.employee.index')}}"
                            class="nav-link {{ request()->is('setting/organization/employee*') ? 'active' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="4" fill="#D0D5DD"/>
                            </svg>
                            <p>พนักงาน</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('setting.organization.company.index')}}"
                            class="nav-link {{ request()->is('setting/organization/company*') ? 'active' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="4" fill="#D0D5DD"/>
                            </svg>
                            <p>ข้อมูลบริษัท</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item {{ request()->is('setting/access*', 'setting/assignment*') ? 'menu-open' : '' }}" id="main-sub-menu">
                <a href="#" class="nav-link {{ request()->is('setting/access*', 'setting/assignment*') ? 'current-page' : '' }}" id="open-sub-menu">
                    <span class="material-symbols-outlined" >
                        person_check
                    </span>
                    <p>
                        การใช้งาน
                    </p>
                    <span class="material-symbols-outlined icon-right">
                        keyboard_arrow_right
                    </span>
                </a>
                <ul class="nav nav-treeview" id="sub-menu">
                    <li class="nav-item">
                        <a href="{{route('setting.access.role.index')}}"
                            class="nav-link {{ request()->is('setting/access/role*') ? 'active' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="4" fill="#D0D5DD"/>
                            </svg>
                            <p>สิทธิ์การใช้งาน</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item {{ request()->is('setting/general*') ? 'menu-open' : '' }}" id="main-sub-menu"> 
                <a href="#" class="nav-link {{ request()->is('setting/general*') ? 'current-page' : '' }}" id="open-sub-menu">
                    <span class="material-symbols-outlined" >
                        settings
                    </span>
                    <p>
                        ทั่วไป
                    </p>
                    <span class="material-symbols-outlined icon-right">
                        keyboard_arrow_right
                    </span>
                </a>
                <ul class="nav nav-treeview" id="sub-menu">
                    <li class="nav-item">
                        <a href="{{route('setting.general.companydepartment.index')}}"
                            class="nav-link {{ request()->is('setting/general/companydepartment*') ? 'active' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="4" fill="#D0D5DD"/>
                            </svg>
                            <p>แผนก</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('setting.general.user-position.index')}}"
                            class="nav-link {{ request()->is('setting/general/user-position*') ? 'active' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="4" fill="#D0D5DD"/>
                            </svg>
                            <p>ตำแหน่ง</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('setting.general.tax')}}"
                            class="nav-link {{ request()->is('setting.general.tax*') ? 'active' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="4" fill="#D0D5DD"/>
                            </svg>
                            <p>ประกันสังคมและภาษี</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('setting.general.searchfield.index')}}"
                            class="nav-link {{ request()->is('setting/general/searchfield*') ? 'active' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="4" fill="#D0D5DD"/>
                            </svg>
                            <p>ฟิลเตอร์ตาราง</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item {{ request()->is('setting/report*') ? 'menu-open' : '' }}" id="main-sub-menu">
                <a href="#" class="nav-link {{ request()->is('setting/report*') ? 'current-page' : '' }}" id="open-sub-menu">
                    <span class="material-symbols-outlined" >
                        finance
                    </span>
                    <p>
                        รายงาน
                    </p>
                    <span class="material-symbols-outlined icon-right">
                        keyboard_arrow_right
                    </span>
                </a>
                <ul class="nav nav-treeview" id="sub-menu">
                    <li class="nav-item">
                        <a href="{{route('setting.report.user')}}"
                            class="nav-link {{ request()->is('setting/report/user*') ? 'active' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="4" fill="#D0D5DD"/>
                            </svg>
                            <p>ข้อมูลพนักงาน</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('setting.report.expiration')}}"
                            class="nav-link {{ request()->is('setting/report/expiration*') ? 'active' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="4" fill="#D0D5DD"/>
                            </svg>
                            <p>วีซ่า/ใบอนุญาต</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('setting.report.log')}}"
                            class="nav-link {{ request()->is('setting/report/log*') ? 'active' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="4" fill="#D0D5DD"/>
                            </svg>
                            <p>ประวัติใช้งาน (Log)</p>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</div>