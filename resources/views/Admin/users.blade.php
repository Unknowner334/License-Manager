<section class="flex flex-col lg:flex-row gap-4 w-full items-stretch lg:justify-center lg:items-center">
    <div class="flex flex-col min-w-0 lg:w-[95%]">
        <x-card title="Users Registered" class="w-full">
            <div class="overflow-auto relative scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-200">
                <table class="w-full min-w-full divide-y divide-gray-200" id="licenses_table">
                    <thead class="bg-gray-50">
                        <tr class="border border-gray-200">
                            <th class="px-6 py-3 text-left text-sm font-medium text-dark-text uppercase tracking-wider">
                                #
                            </th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-dark-text uppercase tracking-wider">
                                Name
                            </th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-dark-text uppercase tracking-wider">
                                Username
                            </th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-dark-text uppercase tracking-wider">
                                Saldo
                            </th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-dark-text uppercase tracking-wider">
                                Role
                            </th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-dark-text uppercase tracking-wider">
                                Reff
                            </th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-dark-text uppercase tracking-wider">
                                Registrar
                            </th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-dark-text uppercase tracking-wider">
                                Created
                            </th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-dark-text uppercase tracking-wider">
                                Action
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>
        </x-card>
    </div>
</section>