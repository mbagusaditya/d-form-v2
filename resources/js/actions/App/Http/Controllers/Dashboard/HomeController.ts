import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Dashboard\HomeController::__invoke
* @see Http/Controllers/Dashboard/HomeController.php:9
* @route '/dashboard'
*/
const HomeController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: HomeController.url(options),
    method: 'get',
})

HomeController.definition = {
    methods: ["get","head"],
    url: '/dashboard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Dashboard\HomeController::__invoke
* @see Http/Controllers/Dashboard/HomeController.php:9
* @route '/dashboard'
*/
HomeController.url = (options?: RouteQueryOptions) => {
    return HomeController.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Dashboard\HomeController::__invoke
* @see Http/Controllers/Dashboard/HomeController.php:9
* @route '/dashboard'
*/
HomeController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: HomeController.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Dashboard\HomeController::__invoke
* @see Http/Controllers/Dashboard/HomeController.php:9
* @route '/dashboard'
*/
HomeController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: HomeController.url(options),
    method: 'head',
})

export default HomeController