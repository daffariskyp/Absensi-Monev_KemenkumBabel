<x-layouts.layout :title="$title" :active="$active">
    <main class="max-w-screen w-full mx-auto rounded-xl bg-white border z-0">
        <!-- Search and Add Button -->
        <div class="flex items-center justify-between mb-3 pt-3 pr-6">
            <form action="" method="GET" class="w-1/2 flex space-x-4 ml-5" id="filter">
                <div class="w-fit relative z-20">
                     <button id="dropdown-level" type="button" class="inline-flex items-center w-full px-2 sm:py-1 xs:py-1.5 sm:text-sm xs:text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm z-20" onclick="toggleDropdown('levels-dropdown', 'level-icon', event)">
                        <span class="mr-auto" id="level-value">Task Level</span>
                        <svg id="level-icon" xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5 ml-2 transform transition-transform duration-500" viewBox="0 0 20 20"
                            fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M6.293 9.293a1 1 0 011.414 0L10 11.586l2.293-2.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                    <ul id="levels-dropdown"class="w-fit absolute right-0 left-0 mt-2 rounded-md shadow-lg bg-white p-1 space-y-1 transform opacity-0 scale-95 -translate-y-2 hidden transition-all duration-300 ease-out origin-top whitespace-nowrap">
                        <li class="block px-4 py-2 text-black hover:bg-[#C3C3C3] cursor-pointer rounded-md w-full" data-value="" onclick="listRequest('', 'task-level-input', '')">Task Level</li>
                        <li class="block px-4 py-2 text-black hover:bg-[#C3C3C3] cursor-pointer rounded-md w-full" data-value="low" onclick="listRequest('Low', 'task-level-input')">Low</li>
                        <li class="block px-4 py-2 text-black hover:bg-[#C3C3C3] cursor-pointer rounded-md w-full" data-value="medium" onclick="listRequest('Medium', 'task-level-input')">Medium</li>
                        <li class="block px-4 py-2 text-black hover:bg-[#C3C3C3] cursor-pointer rounded-md w-full" data-value="high" onclick="listRequest('High', 'task-level-input')">High</li>
                    </ul>
                    <input type="hidden" name="task_level" id="task-level-input" value="{{ request('task_level') }}">
                </div>
                <!-- Replace the existing task status select with this dropdown -->
                <div class="w-fit relative z-20">
                    <button id="dropdown-status" type="button" class="inline-flex items-center w-full px-2 sm:py-1 xs:py-1.5 sm:text-sm xs:text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm z-20" onclick="toggleDropdown('status-dropdown', 'status-icon', event)">
                        <span class="mr-auto" id="status-value">Task Status</span>
                        <svg id="status-icon" xmlns="http://www.w3.org/2000/svg"class="w-5 h-5 ml-2 transform transition-transform duration-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M6.293 9.293a1 1 0 011.414 0L10 11.586l2.293-2.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <ul id="status-dropdown" class="w-fit absolute right-0 left-0 mt-2 rounded-md shadow-lg bg-white p-1 space-y-1 transform opacity-0 scale-95 -translate-y-2 hidden transition-all duration-300 ease-out origin-top whitespace-nowrap">
                        <li class="block px-4 py-2 text-black hover:bg-[#C3C3C3] cursor-pointer rounded-md w-full" data-value="" onclick="listRequest('', 'task-status-input')">Task Status</li>
                        <li class="block px-4 py-2 text-black hover:bg-[#C3C3C3] cursor-pointer rounded-md w-full" data-value="to-do" onclick="listRequest('To-Do', 'task-status-input')">To-Do</li>
                        <li class="block px-4 py-2 text-black hover:bg-[#C3C3C3] cursor-pointer rounded-md w-full" data-value="in progress" onclick="listRequest('In Progress', 'task-status-input')">In Progress</li>
                        <li class="block px-4 py-2 text-black hover:bg-[#C3C3C3] cursor-pointer rounded-md w-full" data-value="review" onclick="listRequest('Review', 'task-status-input')">Review</li>
                        <li class="block px-4 py-2 text-black hover:bg-[#C3C3C3] cursor-pointer rounded-md w-full" data-value="completed" onclick="listRequest('Completed', 'task-status-input')">Complete</li>
                    </ul>
                    <input type="hidden" name="task_status" id="task-status-input" value="{{ request('task_status') }}">
                </div>
                <div class="w-fit relative z-50">
                    <button id="dropdown-project" type="button" class="inline-flex items-center w-full px-2 sm:py-1 xs:py-1.5 sm:text-sm xs:text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm z-20" onclick="toggleDropdown('project-dropdown', 'project-icon', event)">
                        <span class="mr-auto" id="project-value">Projects</span>
                        <svg id="project-icon" xmlns="http://www.w3.org/2000/svg"class="w-5 h-5 ml-2 transform transition-transform duration-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M6.293 9.293a1 1 0 011.414 0L10 11.586l2.293-2.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <ul id="project-dropdown" class="w-fit absolute right-0 left-0 mt-2 rounded-md shadow-lg bg-white p-1 space-y-1 transform opacity-0 scale-95 -translate-y-2 hidden transition-all duration-300 ease-out origin-top z-50 whitespace-nowrap">
                        <li class="block px-4 py-1 text-black hover:bg-[#C3C3C3] cursor-pointer rounded-md w-full" data-value="" onclick="listRequest('', 'project-input')">Projects</li>
                        @foreach ($projects as $project)
                            <li class="block px-4 py-1 text-black hover:bg-[#C3C3C3] cursor-pointer rounded-md w-full" data-value="" onclick="listRequest('{{$project->id}}', 'project-input')">{{$project->name}}</li>
                        @endforeach
                    </ul>
                    <input type="hidden" name="project" id="project-input" value="{{ request('project') }}">
                </div>
                <div class="w-fit relative z-50">
                    <button id="dropdown-employee" type="button" class="inline-flex items-center w-full px-2 sm:py-1 xs:py-1.5 sm:text-sm xs:text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm z-20" onclick="toggleDropdown('employee-dropdown', 'employee-icon', event)">
                        <span class="mr-auto" id="employee-value">Employees</span>
                        <svg id="employee-icon" xmlns="http://www.w3.org/2000/svg"class="w-5 h-5 ml-2 transform transition-transform duration-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M6.293 9.293a1 1 0 011.414 0L10 11.586l2.293-2.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <ul id="employee-dropdown" class="w-fit absolute right-0 left-0 mt-2 rounded-md shadow-lg bg-white p-1 space-y-1 transform opacity-0 scale-95 -translate-y-2 hidden transition-all duration-300 ease-out origin-top z-50 whitespace-nowrap">
                        <li class="block px-4 py-1 text-black hover:bg-[#C3C3C3] cursor-pointer rounded-md w-full text-center" data-value="" onclick="listRequest('', 'employee-input')">Employees</li>
                        <div class="grid grid-rows-4 grid-flow-col">
                            @foreach ($employees as $emp)
                                <li class="block px-4 py-1 text-black hover:bg-[#C3C3C3] cursor-pointer rounded-md w-full" data-value="to-do" onclick="listRequest('{{$emp->employee->id}}', 'employee-input')">{{$emp->name}}</li>
                            @endforeach
                        </div>
                    </ul>
                    <input type="hidden" name="employee" id="employee-input" value="{{ request('employee') }}">
                </div>
            </form>

            <a href="{{ route('tasks.index') }}"
                class="bg-primary-black hover:bg-zinc-500 cursor-pointer text-white text-xs px-4 py-1 rounded-lg hover:bg-gray-800">
                Task Board View
            </a>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto xs:mx-5 sm:mx-0 xs:mt-5 sm:mt-0 sm:bg-none xs:bg-white sm:shadow-none xs:shadow-[0_0_6px_rgba(0,0,0,0.1)] sm:rounded-none xs:rounded-t-lg xs:rounded-b-sm">
            <table class="w-full border-collapse sm:bg-none">
                <thead class="bg-[#F9F9F9]">
                    <tr>
                        <th class="w-[2%] pl-4 py-5 primary-gray text-left font-medium sm:text-sm xs:text-[12px]">#</th>
                        <th class="w-[10%] px-4 py-5 primary-gray text-left font-medium sm:text-sm xs:text-[12px]">Task <span class="hidden sm:inline">Name</span></th>
                        <th class="w-[10%] px-4 py-5 primary-gray text-left font-medium sm:text-sm xs:text-[12px]">Project</th>
                        <th class="w-[10%] px-2 py-5 primary-gray text-left font-medium sm:text-sm xs:text-[12px]"><span class="hidden sm:inline">Assigned</span>
                            Employee
                        </th>
                        <th class="w-[4%] py-5 primary-gray text-left font-medium text-sm text-center hidden sm:table-cell">Task Level
                        </th>
                        <th class="w-[5%] px-4 py-5 primary-gray text-left font-medium text-sm hidden sm:table-cell">Task Status</th>
                        <th class="w-[6%] py-5 primary-gray text-left font-medium text-sm w-24 hidden sm:table-cell">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <!-- Row -->
                    @foreach ($tasks as $index => $task)
                        <tr class="group hover:bg-[#F5F5F5] cursor-pointer font-medium">
                            <td class="pl-4 py-3 sm:text-sm xs:text-[12px]">
                                {{ $tasks->firstItem() + $index }}
                            </td>
                            <td class="px-4 py-3 sm:text-sm xs:text-[12px]">
                                {{ $task->name }}
                            </td>
                            <td class="px-4 py-3 sm:text-sm xs:text-[12px]">
                                {{ $task->project->name }}
                            </td>
                            <td class="pl-2 py-3 sm:text-sm xs:text-[12px]">
                                {{ $task->assignedProjectEmployee->employee->user->name }}
                            </td>
                            <td class="py-3 text-sm flex items-center justify-center hidden sm:flex">
                                <p class="text-white text-xs px-2 rounded-md"
                                    style="background-color: {{ $task->taskLevel->color }};">
                                    {{ $task->taskLevel->name }}
                                </p>
                            </td>
                            <td class="px-4 py-3 text-sm hidden sm:table-cell">
                                {{ $task->taskStatus->name }}
                            </td>
                            <td class="py-3 text-sm hidden sm:table-cell">
                                {{ $task->created_at->format('M j, Y') }}
                            </td>
                            <!-- Row -->
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="flex justify-between items-center my-5 pl-5">
                {{ $tasks->withQueryString()->links() }}
            </div>
        </div>
    </main>
    <script>
        const projects = @js($projects);
        const employees = @js($employees);
        function toggleDropdown(dropdownId, iconId, event) {
            event.stopPropagation();
            const dropdown = document.getElementById(dropdownId);
            const icon = document.getElementById(iconId);

            // Close other dropdown if open
            const otherDropdownId = dropdownId === 'levels-dropdown' ? 'status-dropdown' : 'levels-dropdown';
            const otherIconId = iconId === 'level-icon' ? 'status-icon' : 'level-icon';
            const otherDropdown = document.getElementById(otherDropdownId);
            const otherIcon = document.getElementById(otherIconId);


            if (otherDropdown && !otherDropdown.classList.contains('hidden')) {
                otherDropdown.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
                otherDropdown.classList.add('opacity-0', 'scale-95', '-translate-y-2');
                otherIcon.classList.remove('rotate-180');
                setTimeout(() => {
                    otherDropdown.classList.add('hidden');
                }, 300);
            }

            if (dropdown.classList.contains("hidden")) {
                dropdown.classList.remove('hidden');
                setTimeout(() => {
                    dropdown.classList.remove('opacity-0', 'scale-95', '-translate-y-2');
                    dropdown.classList.add('opacity-100', 'scale-100', 'translate-y-0');
                    icon.classList.add('rotate-180');
                }, 10)
            } else {
                dropdown.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
                dropdown.classList.add('opacity-0', 'scale-95', '-translate-y-2');
                icon.classList.remove('rotate-180');
                setTimeout(() => {
                    dropdown.classList.add('hidden');
                }, 300);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const showLevel = document.getElementById('level-value');
            const showStatus = document.getElementById('status-value');
            const showProject = document.getElementById('project-value');
            const showEmployee = document.getElementById('employee-value');
            const param = new URLSearchParams(window.location.search);
            const status = param.get('task_status');
            const level = param.get('task_level');
            const project = param.get('project');
            const employee = param.get('employee');

            level ? showLevel.textContent = level : showLevel.textContent = "Task Level";
            status ? showStatus.textContent = status : showStatus.textContent = "Task Status";
            project ? showProject.textContent = projects.find(prj => prj.id == project).name : showProject.textContent = "Projects";
            employee ? showEmployee.textContent = employees.find(emp => emp.employee.id == employee).name : showEmployee.textContent = "Employees";

            // Close dropdowns when clicking on the document
            document.addEventListener('click', (event) => {
                const levelsDropdown = document.getElementById('levels-dropdown');
                const statusDropdown = document.getElementById('status-dropdown');
                const levelIcon = document.getElementById('level-icon');
                const statusIcon = document.getElementById('status-icon');
                const levelButton = document.getElementById('dropdown-level');
                const statusButton = document.getElementById('dropdown-status');

                // Check if click is outside of level dropdown-level
                if (levelsDropdown && !levelsDropdown.contains(event.target) && !levelButton.contains(event.target)) {
                    levelsDropdown.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
                    levelsDropdown.classList.add('opacity-0', 'scale-95', '-translate-y-2');
                    levelIcon.classList.remove('rotate-180');
                    setTimeout(() => {
                        levelsDropdown.classList.add('hidden');
                    }, 300);
                }

                // Check if click is outside of status dropdown-level
                if (statusDropdown && !statusDropdown.contains(event.target) && !statusButton.contains(event.target)) {
                    statusDropdown.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
                    statusDropdown.classList.add('opacity-0', 'scale-95', '-translate-y-2');
                    statusIcon.classList.remove('rotate-180');
                    setTimeout(() => {
                        statusDropdown.classList.add('hidden');
                    }, 300);
                }
            });
        });

        function listRequest(param, element) {
            document.getElementById(element).value = param;

             if (element === 'task-level-input') {
                 document.getElementById('level-value').textContent = param || 'Task Level';
             } else if (element === 'task-status-input') {
                 document.getElementById('status-value').textContent = param || 'Task Status';
             }

            document.querySelector('#filter').submit();
        }
    </script>
</x-layouts.layout>