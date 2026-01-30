<?php

function navigationLinkClass(
    string $navigationArea,
    string $routeName
): string {
    $navigationStyles = [
        'topBar' => [
            'active' => 'relative sm:text-neutral-900 sm:dark:text-white
                bg-neutral-50 dark:bg-neutral-800 
                hover:bg-neutral-100 dark:hover:bg-neutral-800
                after:absolute after:-bottom-0.5 after:left-1/2
                after:-translate-x-1/2 after:w-12 after:h-0.5
                after:bg-amber-500 after:rounded-full
                
                sm:after:block after:hidden text-amber-600 dark:text-amber-400',

            'inactive' => 'text-neutral-600 dark:text-neutral-300
                hover:bg-neutral-100 dark:hover:bg-white/5',
        ],

        'bottomBar' => [
            'active' => 'text-amber-600 dark:text-amber-400
                dark:hover:bg-neutral-700',

            'inactive' => 'text-neutral-600 dark:text-neutral-300
                hover:bg-neutral-100 dark:hover:bg-neutral-700',
        ],
    ];

    return request()->routeIs($routeName)
        ? $navigationStyles[$navigationArea]['active']
        : $navigationStyles[$navigationArea]['inactive'];
}
